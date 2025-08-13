<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // Middleware سراسری برای همه درخواست‌ها (API و web)
    protected $middleware = [
        // می‌تونی اینو خالی بذاری چون الان فقط API داری
    ];

    // گروه middleware برای web و api
    protected $middlewareGroups = [
        'web' => [
            // چون الان فقط API داریم، می‌تونی خالی بذاری
        ],

        'api' => [
            'throttle:api',  // محدودیت نرخ درخواست
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    // middleware های قابل فراخوانی در route
    protected $routeMiddleware = [
        'auth' => \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        'is_admin' => \App\Http\Middleware\IsAdmin::class,
    ];
}
