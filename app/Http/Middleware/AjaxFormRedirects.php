<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\ViewErrorBag;
use Symfony\Component\HttpFoundation\Response;

class AjaxFormRedirects
{
    /**
     * Forms are submitted with fetch so a failed submit never adds a history entry. Controllers still
     * answer with redirects, so turn those into JSON: validation errors become a 422 the page shows as a
     * toast, and any other redirect becomes a target URL the page swaps in without growing history.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $request->ajax() || ! $response instanceof RedirectResponse) {
            return $response;
        }

        $errors = $request->session()->get('errors');

        if ($errors instanceof ViewErrorBag && $errors->any()) {
            $request->session()->forget(['errors', '_old_input']);

            return response()->json([
                'message' => $errors->first(),
                'errors' => $errors->getBag('default')->messages(),
            ], 422);
        }

        return response()->json(['redirect' => $response->getTargetUrl()]);
    }
}
