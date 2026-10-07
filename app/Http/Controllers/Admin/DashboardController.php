<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\SalesTransaction;
use App\Models\SalesTransactionItem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;

class DashboardController extends Controller
{
    /**
     * Show a quick overview of the shop: sales today, low stock, staff count.
     */
    public function index(): View
    {
        $todaysSales = SalesTransaction::whereDate('transaction_date', today())->where('status', '!=', 'voided')->sum('total_amount');
        $todaysTransactionCount = SalesTransaction::whereDate('transaction_date', today())->where('status', '!=', 'voided')->count();
        $lowStockItems = InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->limit(5)->get();
        $totalProducts = Product::count();
        $totalStaff = Credential::count();
        $recentTransactions = SalesTransaction::with('credential')->latest('transaction_date')->limit(5)->get();

        [$topSellers, $slowMovers] = $this->productPerformance();

        $lacking = Product::unavailableNow();
        $unavailableProducts = Product::whereIn('id', $lacking->keys())->orderBy('product_name')->get()
            ->map(fn (Product $product) => (object) ['product' => $product, 'lacking' => $lacking[$product->id]]);

        return view('admin.dashboard', [
            'todaysSales' => $todaysSales,
            'todaysTransactionCount' => $todaysTransactionCount,
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
     * Rank every product by units sold over the last 30 days (completed
     * sales only). A product is a "slow mover" when it sold below the
     * average for all products in that window.
     *
     * @return array{0: Collection, 1: Collection}
     */
    private function productPerformance(): array
    {
        $periodStart = now()->subDays(30);

        $soldByProduct = SalesTransactionItem::whereHas('salesTransaction', function ($query) use ($periodStart) {
            $query->where('status', '!=', 'voided')->where('transaction_date', '>=', $periodStart);
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
