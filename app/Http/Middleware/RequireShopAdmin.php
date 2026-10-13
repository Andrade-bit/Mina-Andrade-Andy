<?php

namespace App\Http\Middleware;

use App\Support\ShopContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireShopAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        abort_unless($user && $user->is_active && in_array($user->role, ['owner', 'manager'], true)
            && (int) $user->shop_id === app(ShopContext::class)->id(), 403);
        if ($user->role === 'manager' && $request->routeIs('admin.users*')) {
            abort(403);
        }

        return $next($request);
    }
}
