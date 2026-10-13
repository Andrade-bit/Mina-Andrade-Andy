<?php

namespace App\Http\Middleware;

use App\Support\ShopContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforcePlanFeatures
{
    public function handle(Request $request, Closure $next): Response
    {
        $feature = match (true) {
            $request->routeIs('admin.inventory*', 'admin.suppliers.*', 'admin.supply-purchases.*') => 'inventory',
            $request->routeIs('admin.reports.*', 'pos.transactions*') => 'reports',
            $request->routeIs('admin.expense*') => 'expenses',
            $request->routeIs('admin.promos.*', 'pos.promos.*') => 'promos',
            $request->is('pos/*') && ! $request->routeIs('pos.logout') => 'pos',
            default => null,
        };
        $plan = app(ShopContext::class)->shop()?->plan;
        if ($feature && $plan) {
            abort_unless($plan->allows($feature), 403, 'Your shop plan does not include this feature. Contact your shop owner or platform support.');
        }
        if ($request->is('pos/*') && ! $request->routeIs('pos.logout') && $plan) {
            abort_unless($plan->allows('pos'), 403, 'Your shop plan does not include point of sale.');
            if ($request->filled('promo_code')) {
                abort_unless($plan->allows('promos'), 403, 'Your shop plan does not include promotions.');
            }
        }

        return $next($request);
    }
}
