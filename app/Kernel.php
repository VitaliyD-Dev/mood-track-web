<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // Глобальні міддлвейри
    ];

    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\UpdateLastSeen::class,
        ],
        'api' => [
            // Міддлвейри для API-групи
        ],
    ];

    protected $routeMiddleware = [
        // Міддлвейри для маршрутів
    ];
}