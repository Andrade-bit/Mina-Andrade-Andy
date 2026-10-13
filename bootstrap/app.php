<?php

use App\Http\Middleware\AjaxFormRedirects;
use App\Http\Middleware\EnforcePlanFeatures;
use App\Http\Middleware\EnforceShopLimits;
use App\Http\Middleware\RequireShopAdmin;
use App\Http\Middleware\RequireSuperAdmin;
use App\Http\Middleware\ResolveShop;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trimStrings(except: ['owner_password', 'owner_password_confirmation']);
        // Vercel and most hosts put a proxy in front of the app; trust it so https links and cookies come out right.
        $middleware->trustProxies(at: '*');
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->web(append: [AjaxFormRedirects::class]);
        $middleware->web(append: [ResolveShop::class, EnforcePlanFeatures::class]);
        $middleware->prependToPriorityList(SubstituteBindings::class, ResolveShop::class);
        $middleware->alias([
            'superadmin' => RequireSuperAdmin::class,
            'shop-admin' => RequireShopAdmin::class,
            'shop-limits' => EnforceShopLimits::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash(['owner_password', 'owner_password_confirmation']);
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
