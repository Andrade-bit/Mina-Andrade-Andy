<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Product;
use App\Models\SalesTransaction;
use App\Models\SalesTransactionItem;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * Sales, expenses and profit for a chosen period.
     */
    public function index(Request $request): View
    {
        [$from, $to, $range] = $this->resolvePeriod($request);

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            ...$this->buildReport($from, $to),
        ]);
    }

    /**
     * Download the same report as a CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolvePeriod($request);
        $report = $this->buildReport($from, $to);
        $summary = $report['summary'];

        $filename = 'catbrews-report-'.$from->toDateString().'-to-'.$to->toDateString().'.csv';

        return response()->streamDownload(function () use ($report, $summary, $from, $to) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Catbrews report', $from->toDateString().' to '.$to->toDateString()]);
            fputcsv($out, []);
            fputcsv($out, ['Gross sales', $summary['gross']]);
            fputcsv($out, ['Promo discounts', $summary['discounts']]);
            fputcsv($out, ['Net sales', $summary['net_sales']]);
            fputcsv($out, ['Total expenses', $summary['expenses']]);
            fputcsv($out, ['Net profit', $summary['profit']]);
            fputcsv($out, ['Voided sales (excluded)', $summary['voided']]);
            fputcsv($out, []);
            fputcsv($out, ['Date', 'Sales', 'Discounts', 'Voids', 'Expenses', 'Net']);

            foreach ($report['daily'] as $day) {
                fputcsv($out, [$day['date'], $day['gross'], $day['discounts'], $day['voided'], $day['expenses'], $day['net']]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Turn the request's preset or custom dates into a from/to pair.
     *
     * @return array{0: CarbonImmutable, 1: CarbonImmutable, 2: string}
     */
    private function resolvePeriod(Request $request): array
    {
        $today = CarbonImmutable::today();
        $range = $request->string('range')->toString();

        if ($range === '' && ($request->filled('from') || $request->filled('to'))) {
            $range = 'custom';
        }

        [$from, $to] = match ($range) {
            'today' => [$today, $today],
            'week' => [$today->startOfWeek(), $today],
            'last_month' => [$today->subMonthNoOverflow()->startOfMonth(), $today->subMonthNoOverflow()->endOfMonth()->startOfDay()],
            'custom' => [
                $request->filled('from') ? $request->date('from')->toImmutable()->startOfDay() : $today->startOfMonth(),
                $request->filled('to') ? $request->date('to')->toImmutable()->startOfDay() : $today,
            ],
            default => [$today->startOfMonth(), $today],
        };

        if ($range === '' || ! in_array($range, ['today', 'week', 'last_month', 'custom'], true)) {
            $range = 'month';
        }

        if ($to->lessThan($from)) {
            [$from, $to] = [$to, $from];
        }

        return [$from, $to, $range];
    }

    /**
     * @return array{summary: array<string, float>, daily: list<array<string, mixed>>, byPayment: Collection, byCategory: Collection, topProducts: Collection, chartMax: float}
     */
    private function buildReport(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $start = $from->startOfDay();
        $end = $to->endOfDay();

        $completed = SalesTransaction::whereBetween('transaction_date', [$start, $end])->where('status', '!=', 'voided');
        $voidedTotal = (float) SalesTransaction::whereBetween('transaction_date', [$start, $end])->where('status', 'voided')->sum('total_amount');
        $voidedCount = SalesTransaction::whereBetween('transaction_date', [$start, $end])->where('status', 'voided')->count();

        $netSales = (float) (clone $completed)->sum('total_amount');
        $discounts = (float) (clone $completed)->sum('discount_amount');
        $gross = $netSales + $discounts;
        $salesCount = (clone $completed)->count();
        $promoCount = (clone $completed)->whereNotNull('promo_id')->count();
        $expenseTotal = (float) Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])->sum('amount');

        $salesByDay = (clone $completed)
            ->selectRaw('DATE(transaction_date) as d, SUM(total_amount) as net, SUM(discount_amount) as disc')
            ->groupBy('d')->get()->keyBy('d');
        $voidsByDay = SalesTransaction::whereBetween('transaction_date', [$start, $end])->where('status', 'voided')
            ->selectRaw('DATE(transaction_date) as d, SUM(total_amount) as total')
            ->groupBy('d')->pluck('total', 'd');
        $expensesByDay = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('expense_date as d, SUM(amount) as total')
            ->groupBy('d')->pluck('total', 'd');

        $daily = [];
        $chartMax = 0.0;

        for ($day = $from; $day->lessThanOrEqualTo($to); $day = $day->addDay()) {
            $key = $day->toDateString();
            $net = (float) ($salesByDay[$key]->net ?? 0);
            $disc = (float) ($salesByDay[$key]->disc ?? 0);
            $exp = (float) ($expensesByDay[$key] ?? 0);

            $daily[] = [
                'date' => $key,
                'label' => $day->format('M j'),
                'gross' => $net + $disc,
                'discounts' => $disc,
                'voided' => (float) ($voidsByDay[$key] ?? 0),
                'expenses' => $exp,
                'net' => $net - $exp,
            ];
            $chartMax = max($chartMax, $net + $disc, $exp);
        }

        $byPayment = (clone $completed)
            ->selectRaw('payment_method, SUM(total_amount) as total, COUNT(*) as count')
            ->groupBy('payment_method')->orderByDesc('total')->get();

        $byCategory = Expense::whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->select(DB::raw('COALESCE(expense_categories.category_name, "Uncategorized") as category_name'), DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('category_name')->orderByDesc('total')->get();

        $topRows = SalesTransactionItem::whereHas('salesTransaction', fn ($q) => $q->whereBetween('transaction_date', [$start, $end])->where('status', '!=', 'voided'))
            ->selectRaw('product_id, SUM(quantity) as sold')
            ->groupBy('product_id')->orderByDesc('sold')->limit(5)->get();
        $names = Product::withTrashed()->whereIn('id', $topRows->pluck('product_id'))->pluck('product_name', 'id');
        $topProducts = $topRows->map(fn ($row) => (object) ['name' => $names[$row->product_id] ?? 'Unknown', 'sold' => (int) $row->sold]);

        return [
            'summary' => [
                'gross' => round($gross, 2),
                'discounts' => round($discounts, 2),
                'net_sales' => round($netSales, 2),
                'voided' => round($voidedTotal, 2),
                'expenses' => round($expenseTotal, 2),
                'profit' => round($netSales - $expenseTotal, 2),
                'sales_count' => $salesCount,
                'promo_count' => $promoCount,
                'voided_count' => $voidedCount,
            ],
            'daily' => $daily,
            'byPayment' => $byPayment,
            'byCategory' => $byCategory,
            'topProducts' => $topProducts,
            'chartMax' => $chartMax,
        ];
    }
}
