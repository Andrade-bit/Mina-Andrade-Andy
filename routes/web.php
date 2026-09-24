<?php

use App\Http\Controllers\Admin\CupSizeController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExpenseCategoryController;
use App\Http\Controllers\Admin\ExpenseController;
use App\Http\Controllers\Admin\InventoryItemController;
use App\Http\Controllers\Admin\InventoryTransactionController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SalesReportController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\SupplyPurchaseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\PosAuthController;
use App\Http\Controllers\Pos\TerminalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.login');
});

Route::get('/admin/login', [AdminAuthController::class, 'create'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'store'])->name('admin.login.store');
Route::post('/admin/logout', [AdminAuthController::class, 'destroy'])->name('admin.logout');

Route::middleware('auth')->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    Route::post('/admin/users', [UserController::class, 'store'])->name('admin.users.store');
    Route::put('/admin/users/{credential}', [UserController::class, 'update'])->name('admin.users.update');
    Route::delete('/admin/users/{credential}', [UserController::class, 'destroy'])->name('admin.users.destroy');

    Route::get('/admin/inventory', [InventoryItemController::class, 'index'])->name('admin.inventory');
    Route::get('/admin/inventory-items', [InventoryItemController::class, 'index'])->name('admin.inventory-items.index');
    Route::post('/admin/inventory-items', [InventoryItemController::class, 'store'])->name('admin.inventory-items.store');
    Route::put('/admin/inventory-items/{inventoryItem}', [InventoryItemController::class, 'update'])->name('admin.inventory-items.update');
    Route::delete('/admin/inventory-items/{inventoryItem}', [InventoryItemController::class, 'destroy'])->name('admin.inventory-items.destroy');

    Route::get('/admin/inventory-transactions', [InventoryTransactionController::class, 'index'])->name('admin.inventory-transactions.index');
    Route::post('/admin/inventory-transactions/stock-in', [InventoryTransactionController::class, 'stockIn'])->name('admin.inventory-transactions.stock-in');
    Route::post('/admin/inventory-transactions/stock-out', [InventoryTransactionController::class, 'stockOut'])->name('admin.inventory-transactions.stock-out');

    Route::get('/admin/product-categories', [ProductCategoryController::class, 'index'])->name('admin.product-categories.index');
    Route::post('/admin/product-categories', [ProductCategoryController::class, 'store'])->name('admin.product-categories.store');
    Route::put('/admin/product-categories/{productCategory}', [ProductCategoryController::class, 'update'])->name('admin.product-categories.update');
    Route::delete('/admin/product-categories/{productCategory}', [ProductCategoryController::class, 'destroy'])->name('admin.product-categories.destroy');

    Route::get('/admin/products', [ProductController::class, 'index'])->name('admin.products.index');
    Route::get('/admin/products/create', [ProductController::class, 'create'])->name('admin.products.create');
    Route::post('/admin/products', [ProductController::class, 'store'])->name('admin.products.store');
    Route::get('/admin/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
    Route::put('/admin/products/{product}', [ProductController::class, 'update'])->name('admin.products.update');
    Route::delete('/admin/products/{product}', [ProductController::class, 'destroy'])->name('admin.products.destroy');

    Route::get('/admin/cup-sizes', [CupSizeController::class, 'index'])->name('admin.cup-sizes.index');
    Route::post('/admin/cup-sizes', [CupSizeController::class, 'store'])->name('admin.cup-sizes.store');
    Route::put('/admin/cup-sizes/{cupSize}', [CupSizeController::class, 'update'])->name('admin.cup-sizes.update');
    Route::delete('/admin/cup-sizes/{cupSize}', [CupSizeController::class, 'destroy'])->name('admin.cup-sizes.destroy');

    Route::get('/admin/suppliers', [SupplierController::class, 'index'])->name('admin.suppliers.index');
    Route::post('/admin/suppliers', [SupplierController::class, 'store'])->name('admin.suppliers.store');
    Route::put('/admin/suppliers/{supplier}', [SupplierController::class, 'update'])->name('admin.suppliers.update');
    Route::delete('/admin/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('admin.suppliers.destroy');

    Route::get('/admin/supply-purchases', [SupplyPurchaseController::class, 'index'])->name('admin.supply-purchases.index');
    Route::get('/admin/supply-purchases/{supplyPurchase}', [SupplyPurchaseController::class, 'show'])->name('admin.supply-purchases.show');
    Route::post('/admin/supply-purchases', [SupplyPurchaseController::class, 'store'])->name('admin.supply-purchases.store');

    Route::get('/admin/expense-categories', [ExpenseCategoryController::class, 'index'])->name('admin.expense-categories.index');
    Route::post('/admin/expense-categories', [ExpenseCategoryController::class, 'store'])->name('admin.expense-categories.store');
    Route::put('/admin/expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'update'])->name('admin.expense-categories.update');
    Route::delete('/admin/expense-categories/{expenseCategory}', [ExpenseCategoryController::class, 'destroy'])->name('admin.expense-categories.destroy');

    Route::get('/admin/expenses', [ExpenseController::class, 'index'])->name('admin.expenses.index');
    Route::get('/admin/expenses/create', [ExpenseController::class, 'create'])->name('admin.expenses.create');
    Route::post('/admin/expenses', [ExpenseController::class, 'store'])->name('admin.expenses.store');
    Route::delete('/admin/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('admin.expenses.destroy');
});

Route::get('/pos/login', [PosAuthController::class, 'create'])->name('pos.login');
Route::post('/pos/login', [PosAuthController::class, 'store'])->name('pos.login.store');
Route::post('/pos/logout', [PosAuthController::class, 'destroy'])->name('pos.logout');

Route::get('/pos/terminal', [TerminalController::class, 'index'])->name('pos.terminal');
Route::post('/pos/terminal', [TerminalController::class, 'store'])->name('pos.terminal.store');

Route::get('/pos/transactions', [SalesReportController::class, 'index'])->name('pos.transactions');
