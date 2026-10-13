<?php

namespace App\Providers;

use App\Models\Credential;
use App\Models\Expense;
use App\Models\InventoryBatch;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use App\Models\PlatformSetting;
use App\Models\Product;
use App\Models\Promo;
use App\Models\SalesTransaction;
use App\Models\Supplier;
use App\Models\SupplyPurchase;
use App\Models\User;
use App\Observers\AccountChangeObserver;
use App\Observers\ShopAuditObserver;
use App\Support\ShopContext;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(ShopContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        User::observe(AccountChangeObserver::class);
        foreach ([InventoryItem::class, InventoryTransaction::class, SupplyPurchase::class, SalesTransaction::class, Product::class, Credential::class, Expense::class, Promo::class, Supplier::class, InventoryBatch::class] as $model) {
            $model::observe(ShopAuditObserver::class);
        }
        View::composer('superadmin.layout', function ($view): void {
            $view->with('platformName', PlatformSetting::find(1)?->platform_name ?? 'Catbrews');
        });
        View::composer('admin.partials.sidebar', function ($view) {
            $view->with('currentShop', app(ShopContext::class)->shop());
            $view->with('sidebarLowStockCount', InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->count());
        });
    }
}
