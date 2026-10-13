<?php

namespace App\Providers;

use App\Models\InventoryItem;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->scoped(\App\Support\ShopContext::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('superadmin.layout', function ($view): void {
            $view->with('platformName', \App\Models\PlatformSetting::find(1)?->platform_name ?? 'Catbrews');
        });
        View::composer('admin.partials.sidebar', function ($view) {
            $view->with('currentShop', app(\App\Support\ShopContext::class)->shop());
            $view->with('sidebarLowStockCount', InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->count());
        });
    }
}
