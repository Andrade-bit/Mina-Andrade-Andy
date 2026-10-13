<?php

namespace App\Http\Middleware;

use App\Models\Credential;
use App\Models\Product;
use App\Models\Shop;
use App\Support\ShopContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class EnforceShopLimits
{
    public function handle(Request $request, Closure $next): Response
    {
        $product = $request->routeIs('admin.products.store', 'admin.products.restore');
        $staff = $request->routeIs('admin.users.store', 'admin.users.restore');
        if (! $product && ! $staff) {
            return $next($request);
        }

        return DB::transaction(function () use ($next, $request, $product): Response {
            $shop = Shop::with('plan')->lockForUpdate()->findOrFail(app(ShopContext::class)->id());
            $limit = $product ? $shop->plan->max_products : $shop->plan->max_staff;
            $count = $product ? Product::count() : Credential::count();
            if ($limit !== null && $count >= $limit) {
                throw ValidationException::withMessages(['plan' => 'This shop has reached its plan limit for '.($product ? 'active products' : 'active POS staff').'. Contact platform support to change plans.']);
            }

            return $next($request);
        });
    }
}
