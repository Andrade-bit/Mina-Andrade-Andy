<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Credential;
use App\Models\InventoryItem;
use App\Models\Product;
use App\Models\SalesTransaction;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    /**
     * Show a quick overview of the shop: sales today, low stock, staff count.
     */
    public function index(): View
    {
        $todaysSales = SalesTransaction::whereDate('transaction_date', today())->sum('total_amount');
        $todaysTransactionCount = SalesTransaction::whereDate('transaction_date', today())->count();
        $lowStockItems = InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->limit(5)->get();
        $totalProducts = Product::count();
        $totalStaff = Credential::count();
        $recentTransactions = SalesTransaction::with('credential')->latest('transaction_date')->limit(5)->get();

        return view('admin.dashboard', [
            'todaysSales' => $todaysSales,
            'todaysTransactionCount' => $todaysTransactionCount,
            'lowStockItems' => $lowStockItems,
            'totalProducts' => $totalProducts,
            'totalStaff' => $totalStaff,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
