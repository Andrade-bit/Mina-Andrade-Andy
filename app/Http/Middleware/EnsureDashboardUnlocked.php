<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureDashboardUnlocked
{
    /**
     * The admin login lives in the browser session, and the POS is used in the same browser. Once the POS has
     * been opened there (see LockDashboardForPos) the dashboard stays locked until the admin types their
     * password again, so whoever is at the POS cannot walk into the admin pages.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->session()->get('dashboard_locked')) {
            return redirect()->guest(route('admin.unlock'));
        }

        return $next($request);
    }
}
