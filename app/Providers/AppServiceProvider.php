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
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('admin.partials.sidebar', function ($view) {
            $view->with('sidebarLowStockCount', InventoryItem::whereColumn('current_quantity', '<=', 'reorder_level')->count());
        });
    }
}
