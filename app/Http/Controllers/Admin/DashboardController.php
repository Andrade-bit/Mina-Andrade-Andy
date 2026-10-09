<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\SalesTransaction;
use App\Models\SalesTransactionItem;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Show a quick overview of the shop: sales today, low stock, staff count.
     */
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => ['nullable', 'date_format:Y-m-d', ...($request->filled('from') ? ['after_or_equal:from'] : [])],
        ]);
        $today = CarbonImmutable::today('Asia/Manila')->toDateString();
        $from = $validated['from'] ?? $validated['to'] ?? $today;
        $to = $validated['to'] ?? $from;
        $periodStart = CarbonImmutable::parse($from, 'Asia/Manila')->setTimezone(config('app.timezone'));
        $periodEnd = CarbonImmutable::parse($to, 'Asia/Manila')->addDay()->setTimezone(config('app.timezone'));
        $sales = SalesTransaction::where('transaction_date', '>=', $periodStart)->where('transaction_date', '<', $periodEnd);
        $completed = (clone $sales)->where('status', '!=', 'voided');
        $periodSales = (clone $completed)->sum('total_amount');
        $periodTransactionCount = (clone $completed)->count();
        $lowStockItems = InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->limit(5)->get();
        $totalProducts = Product::count();
        $totalStaff = Credential::count();
        $recentTransactions = (clone $sales)->with('credential')->latest('transaction_date')->latest('id')->limit(5)->get();

        [$topSellers, $slowMovers] = $this->productPerformance($periodStart, $periodEnd);

        $lacking = Product::unavailableNow();
        $unavailableProducts = Product::whereIn('id', $lacking->keys())->orderBy('product_name')->get()
            ->map(fn (Product $product) => (object) ['product' => $product, 'lacking' => $lacking[$product->id]]);

        return view('admin.dashboard', [
            'expiryAlerts' => InventoryBatch::alerts()->get(),
            'periodSales' => $periodSales,
            'periodTransactionCount' => $periodTransactionCount,
            'from' => $from,
            'to' => $to,
            'periodLabel' => $from === $to ? CarbonImmutable::parse($from)->format('M j, Y') : CarbonImmutable::parse($from)->format('M j, Y').' – '.CarbonImmutable::parse($to)->format('M j, Y'),
            'lowStockItems' => $lowStockItems,
            'totalProducts' => $totalProducts,
            'totalStaff' => $totalStaff,
            'recentTransactions' => $recentTransactions,
            'topSellers' => $topSellers,
            'slowMovers' => $slowMovers,
            'unavailableProducts' => $unavailableProducts,
        ]);
    }

    /**
     * Rank every product by units sold over the selected period (completed
     * sales only). A product is a "slow mover" when it sold below the
     * average for all products in that window.
     *
     * @return array{0: Collection, 1: Collection}
     */
    private function productPerformance(CarbonImmutable $periodStart, CarbonImmutable $periodEnd): array
    {
        $soldByProduct = SalesTransactionItem::whereHas('salesTransaction', function ($query) use ($periodStart, $periodEnd) {
            $query->where('status', '!=', 'voided')->where('transaction_date', '>=', $periodStart)->where('transaction_date', '<', $periodEnd);
        })->selectRaw('product_id, SUM(quantity) as total_qty')
            ->groupBy('product_id')
            ->pluck('total_qty', 'product_id');

        $products = Product::all();

        if ($products->isEmpty()) {
            return [collect(), collect()];
        }

        $performance = $products->map(fn (Product $product) => (object) [
            'product' => $product,
            'sold' => (int) ($soldByProduct[$product->id] ?? 0),
        ]);

        $average = $performance->avg('sold');

        $topSellers = $performance->sortByDesc('sold')->take(5)->values();
        $slowMovers = $performance->filter(fn ($row) => $row->sold < $average)->sortBy('sold')->take(5)->values();

        return [$topSellers, $slowMovers];
    }
}
