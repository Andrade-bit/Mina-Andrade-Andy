<?php

namespace App\Http\Middleware;

use App\Models\Credential;
use App\Models\Shop;
use App\Support\ShopContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ResolveShop
{
    public function __construct(private ShopContext $context) {}

    public function handle(Request $request, Closure $next): Response
    {
        $this->context->set(null);
        try {
            if ($request->routeIs('admin.login*', 'admin.logout', 'account.*', 'internal.notifications')) {
                return $next($request);
            }
            $user = $request->user();
            if ($user?->isSuperAdmin() && $request->is('pos/*') && ! $request->routeIs('pos.logout')) {
                abort(403, 'Use a shop staff account to access the POS.');
            }
            if ($request->routeIs('superadmin.*') || $user?->isSuperAdmin()) {
                return $next($request);
            }
            $shop = null;
            if ($request->is('admin/*') && $user) {
                $shop = Shop::find($user->shop_id);
            } elseif ($request->routeIs('pos.login*') && $request->filled('shop')) {
                $slug = $request->input('shop');
                abort_unless(is_string($slug), 404);
                $shop = Shop::where('slug', $slug)->first();
            } elseif ($request->is('pos/*') && $request->session()->has('pos_credential_id')) {
                $credential = Credential::withoutGlobalScope('shop')->find($request->session()->get('pos_credential_id'));
                $shop = $credential ? Shop::find($credential->shop_id) : null;
                if (! $shop) {
                    $request->session()->forget(['pos_credential_id', 'pos_credential_role']);
                }
            }
            if (! $shop && ! ($request->is('admin/*') && $user)) {
                $slug = $request->input('shop');
                abort_if($slug !== null && ! is_string($slug), 404);
                $shop = $slug ? Shop::where('slug', $slug)->first() : Shop::find(1);
            }
            if ($shop) {
                $this->context->set($shop->id);
                if (! $request->routeIs('admin.login*', 'admin.logout', 'pos.logout')) {
                    abort_unless($shop->canOperate(), 403, 'This shop is unavailable. Please contact your shop owner or platform support.');
                }
            }
            abort_if(! $shop && ! $request->routeIs('admin.login*', 'admin.logout'), 404);

            return $next($request);
        } finally {
            $this->context->reset();
        }
    }
}
