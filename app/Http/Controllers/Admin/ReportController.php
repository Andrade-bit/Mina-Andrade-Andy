<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\Product;
use App\Models\SalesTransaction;
use App\Models\SalesTransactionItem;
use App\Models\SupplyPurchase;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    /**
     * The kinds of report the page can show. Overview is sales and expenses side by side.
     *
     * @var array<string, string>
     */
    private const TYPES = [
        'overview' => 'Overview',
        'sales' => 'Sales',
        'expenses' => 'Expenses',
        'inventory' => 'Inventory',
    ];

    /**
     * Sales, expenses, inventory and profit for a chosen period.
     */
    public function index(Request $request): View
    {
        [$from, $to, $range] = $this->resolvePeriod($request);
        $type = $this->resolveType($request);

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'range' => $range,
            'type' => $type,
            'types' => self::TYPES,
            ...($type === 'inventory' ? [] : $this->buildReport($from, $to)),
            ...match ($type) {
                'sales' => $this->buildSalesReport($from, $to),
                'expenses' => $this->buildExpensesReport($from, $to),
                'inventory' => $this->buildInventoryReport($from, $to),
                default => [],
            },
        ]);
    }

    /**
     * Download the chosen report as a CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        [$from, $to] = $this->resolvePeriod($request);
        $type = $this->resolveType($request);

        $filename = 'catbrews-'.$type.'-report-'.$from->toDateString().'-to-'.$to->toDateString().'.csv';

        return response()->streamDownload(function () use ($from, $to, $type) {
            $out = fopen('php://output', 'w');

            fputcsv($out, ['Catbrews '.strtolower(self::TYPES[$type]).' report', $from->toDateString().' to '.$to->toDateString()]);
            fputcsv($out, []);

            match ($type) {
                'sales' => $this->writeSalesCsv($out, $from, $to),
                'expenses' => $this->writeExpensesCsv($out, $from, $to),
                'inventory' => $this->writeInventoryCsv($out, $from, $to),
                default => $this->writeOverviewCsv($out, $from, $to),
            };

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * @param  resource  $out
     */
    private function writeOverviewCsv($out, CarbonImmutable $from, CarbonImmutable $to): void
    {
        $report = $this->buildReport($from, $to);
        $summary = $report['summary'];

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
    }

    /**
     * @param  resource  $out
     */
    private function writeSalesCsv($out, CarbonImmutable $from, CarbonImmutable $to): void
    {
        $report = $this->buildReport($from, $to);
        $sales = $this->buildSalesReport($from, $to);
        $summary = $report['summary'];

        fputcsv($out, ['Gross sales', $summary['gross']]);
        fputcsv($out, ['Promo discounts', $summary['discounts']]);
        fputcsv($out, ['Net sales', $summary['net_sales']]);
        fputcsv($out, ['Completed sales', $summary['sales_count']]);
        fputcsv($out, ['Voided sales (excluded)', $summary['voided']]);
        fputcsv($out, []);
        fputcsv($out, ['Date', 'Gross sales', 'Discounts', 'Voids']);

        foreach ($report['daily'] as $day) {
            fputcsv($out, [$day['date'], $day['gross'], $day['discounts'], $day['voided']]);
        }

        fputcsv($out, []);
        fputcsv($out, ['Payment method', 'Sales', 'Transactions']);
        foreach ($report['byPayment'] as $row) {
            fputcsv($out, [$row->payment_method, $row->total, $row->count]);
        }

        fputcsv($out, []);
        fputcsv($out, ['Category', 'Sales', 'Units sold']);
        foreach ($sales['salesByCategory'] as $row) {
            fputcsv($out, [$row->name, $row->total, $row->qty]);
        }

        fputcsv($out, []);
        fputcsv($out, ['Processed by', 'Sales', 'Transactions']);
        foreach ($sales['salesByStaff'] as $row) {
            fputcsv($out, [$row->name, $row->total, $row->count]);
        }

        fputcsv($out, []);
        fputcsv($out, ['Product', 'Units sold']);
        foreach ($sales['topProductsFull'] as $row) {
            fputcsv($out, [$row->name, $row->sold]);
        }
    }

    /**
     * @param  resource  $out
     */
    private function writeExpensesCsv($out, CarbonImmutable $from, CarbonImmutable $to): void
    {
        $report = $this->buildReport($from, $to);

        fputcsv($out, ['Total expenses', $report['summary']['expenses']]);
        fputcsv($out, []);
        fputcsv($out, ['Category', 'Total']);
        foreach ($report['byCategory'] as $row) {
            fputcsv($out, [$row->category_name, $row->total]);
        }

        fputcsv($out, []);
        fputcsv($out, ['Date', 'Category', 'Description', 'Payment', 'Amount']);
        foreach ($this->expensesInPeriod($from, $to)->with('expenseCategory')->latest('expense_date')->latest('id')->get() as $expense) {
            fputcsv($out, [$expense->expense_date->toDateString(), $expense->expenseCategory?->category_name ?? 'Uncategorized', $expense->description, $expense->payment_method, $expense->amount]);
        }
    }

    /**
     * @param  resource  $out
     */
    private function writeInventoryCsv($out, CarbonImmutable $from, CarbonImmutable $to): void
    {
        $inventory = $this->buildInventoryReport($from, $to);

        fputcsv($out, ['Item', 'Type', 'Unit', 'On hand', 'Reorder level', 'Status', 'Received', 'Used by sales', 'Waste', 'Adjustments']);
        foreach ($inventory['inventoryRows'] as $row) {
            fputcsv($out, [$row->item->name, $row->item->type === 'supply' ? 'Cups & straws' : 'Ingredient', $row->item->unit, $row->item->current_quantity, $row->item->reorder_level, $row->isLow ? 'Low stock' : 'OK', $row->received, $row->sold, $row->waste, $row->adjusted]);
        }

        fputcsv($out, []);
        fputcsv($out, ['Date', 'Item', 'Type', 'Quantity', 'Reason']);
        foreach ($this->movementsInPeriod($from, $to)->with('inventoryItem')->latest('inventory_transaction_date')->latest('id')->get() as $move) {
            fputcsv($out, [$move->inventory_transaction_date->toDateString(), $move->inventoryItem?->name ?? 'Unknown item', $move->transaction_type, $move->quantity, $move->reason]);
        }
    }

    private function resolveType(Request $request): string
    {
        $type = $request->string('type')->toString();

        return array_key_exists($type, self::TYPES) ? $type : 'overview';
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
     * Expenses dated inside the period.
     *
     * @return Builder<Expense>
     */
    private function expensesInPeriod(CarbonImmutable $from, CarbonImmutable $to)
    {
        return Expense::whereBetween('expense_date', [$from->toDateString(), $to->toDateString()]);
    }

    /**
     * Stock movements dated inside the period.
     *
     * @return Builder<InventoryTransaction>
     */
    private function movementsInPeriod(CarbonImmutable $from, CarbonImmutable $to)
    {
        return InventoryTransaction::whereBetween('inventory_transaction_date', [$from->toDateString(), $to->toDateString()]);
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
        $expenseTotal = (float) $this->expensesInPeriod($from, $to)->sum('amount');

        $salesByDay = (clone $completed)
            ->selectRaw('DATE(transaction_date) as d, SUM(total_amount) as net, SUM(discount_amount) as disc')
            ->groupBy('d')->get()->keyBy('d');
        $voidsByDay = SalesTransaction::whereBetween('transaction_date', [$start, $end])->where('status', 'voided')
            ->selectRaw('DATE(transaction_date) as d, SUM(total_amount) as total')
            ->groupBy('d')->pluck('total', 'd');
        $expensesByDay = $this->expensesInPeriod($from, $to)
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

        $byCategory = $this->expensesInPeriod($from, $to)
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->select(DB::raw('COALESCE(expense_categories.category_name, "Uncategorized") as category_name'), DB::raw('SUM(expenses.amount) as total'))
            ->groupBy('category_name')->orderByDesc('total')->get();

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
            'topProducts' => $this->topProducts($start, $end, 5),
            'chartMax' => $chartMax,
        ];
    }

    /**
     * Best-selling products by units sold in a period, voided sales left out.
     */
    private function topProducts(CarbonImmutable $start, CarbonImmutable $end, int $limit): Collection
    {
        $topRows = SalesTransactionItem::whereHas('salesTransaction', fn ($q) => $q->whereBetween('transaction_date', [$start, $end])->where('status', '!=', 'voided'))
            ->selectRaw('product_id, SUM(quantity) as sold')
            ->groupBy('product_id')->orderByDesc('sold')->limit($limit)->get();
        $names = Product::withTrashed()->whereIn('id', $topRows->pluck('product_id'))->pluck('product_name', 'id');

        return $topRows->map(fn ($row) => (object) ['name' => $names[$row->product_id] ?? 'Unknown', 'sold' => (int) $row->sold]);
    }

    /**
     * What the Sales report adds on top of the shared figures: who sold, what sold, and the sales themselves.
     *
     * @return array{salesByCategory: Collection, salesByStaff: Collection, topProductsFull: Collection, recentSales: Collection}
     */
    private function buildSalesReport(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $start = $from->startOfDay();
        $end = $to->endOfDay();

        $salesByCategory = SalesTransactionItem::query()
            ->join('sales_transactions', 'sales_transactions.id', '=', 'sales_transaction_items.sales_transaction_id')
            ->join('products', 'products.id', '=', 'sales_transaction_items.product_id')
            ->leftJoin('product_categories', 'product_categories.id', '=', 'products.product_category_id')
            ->whereBetween('sales_transactions.transaction_date', [$start, $end])
            ->where('sales_transactions.status', '!=', 'voided')
            ->select(DB::raw('COALESCE(product_categories.category_name, "Uncategorized") as name'), DB::raw('SUM(sales_transaction_items.subtotal) as total'), DB::raw('SUM(sales_transaction_items.quantity) as qty'))
            ->groupBy('name')->orderByDesc('total')->get();

        $salesByStaff = SalesTransaction::query()
            ->leftJoin('credentials', 'credentials.id', '=', 'sales_transactions.credential_id')
            ->whereBetween('sales_transactions.transaction_date', [$start, $end])
            ->where('sales_transactions.status', '!=', 'voided')
            ->select(DB::raw('COALESCE(credentials.first_name, "Unknown") as name'), DB::raw('SUM(sales_transactions.total_amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('name')->orderByDesc('total')->get();

        return [
            'salesByCategory' => $salesByCategory,
            'salesByStaff' => $salesByStaff,
            'topProductsFull' => $this->topProducts($start, $end, 10),
            'recentSales' => SalesTransaction::with('credential')
                ->whereBetween('transaction_date', [$start, $end])
                ->latest('transaction_date')->limit(10)->get(),
        ];
    }

    /**
     * What the Expenses report adds: payment methods, the Stock In share, and the itemised list.
     *
     * @return array{expenseCount: int, purchaseSpend: float, expenseByPayment: Collection, expenseRows: Collection}
     */
    private function buildExpensesReport(CarbonImmutable $from, CarbonImmutable $to): array
    {
        return [
            'expenseCount' => $this->expensesInPeriod($from, $to)->count(),
            'purchaseSpend' => round((float) $this->expensesInPeriod($from, $to)->whereNotNull('supply_purchase_id')->sum('amount'), 2),
            'expenseByPayment' => $this->expensesInPeriod($from, $to)
                ->selectRaw('payment_method, SUM(amount) as total, COUNT(*) as count')
                ->groupBy('payment_method')->orderByDesc('total')->get(),
            'expenseRows' => $this->expensesInPeriod($from, $to)->with('expenseCategory')
                ->latest('expense_date')->latest('id')->limit(100)->get(),
        ];
    }

    /**
     * The Inventory report: stock on hand now, plus what moved in and out during the period.
     *
     * @return array{inventoryRows: Collection, inventoryKpi: array<string, float|int>, movementLog: Collection, periodPurchases: Collection}
     */
    private function buildInventoryReport(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $moved = $this->movementsInPeriod($from, $to)
            ->selectRaw('inventory_item_id, transaction_type, SUM(quantity) as qty')
            ->groupBy('inventory_item_id', 'transaction_type')
            ->get()->groupBy('inventory_item_id');

        $rows = InventoryItem::orderBy('name')->get()->map(function (InventoryItem $item) use ($moved) {
            $byType = $moved->get($item->id, collect())->keyBy('transaction_type');

            return (object) [
                'item' => $item,
                'isLow' => (float) $item->current_quantity <= (float) $item->reorder_level,
                'received' => (float) ($byType['Restock']->qty ?? 0),
                'sold' => (float) ($byType['Sales']->qty ?? 0),
                'waste' => (float) ($byType['Waste']->qty ?? 0),
                'adjusted' => (float) ($byType['Adjustment']->qty ?? 0),
            ];
        })->sortByDesc('isLow')->values();

        $purchases = SupplyPurchase::whereBetween('purchase_date', [$from->toDateString(), $to->toDateString()]);

        return [
            'inventoryRows' => $rows,
            'inventoryKpi' => [
                'items' => $rows->count(),
                'low' => $rows->where('isLow', true)->count(),
                'purchases' => (clone $purchases)->count(),
                'purchaseSpend' => round((float) (clone $purchases)->sum('total_amount'), 2),
                'stockOutMoves' => $this->movementsInPeriod($from, $to)->whereIn('transaction_type', ['Sales', 'Waste', 'Adjustment'])->count(),
            ],
            'movementLog' => $this->movementsInPeriod($from, $to)->with('inventoryItem')
                ->latest('inventory_transaction_date')->latest('id')->limit(25)->get(),
            'periodPurchases' => (clone $purchases)->with('supplier')->latest('purchase_date')->limit(10)->get(),
        ];
    }
}
