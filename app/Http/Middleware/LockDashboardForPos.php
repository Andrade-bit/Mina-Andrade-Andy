<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LockDashboardForPos
{
    /**
     * Opening the POS PIN screen hands the device over to whoever is standing at it. If an admin is signed in
     * to the dashboard in this browser, lock the dashboard so it asks for their password again.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()) {
            $request->session()->put('dashboard_locked', true);
        }

        return $next($request);
    }
}
