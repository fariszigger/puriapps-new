<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'authentication' => \App\Http\Middleware\Authentication::class,
            'admin' => \App\Http\Middleware\Admin::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\UpdateLastActivity::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->dontFlash([
            'password',
            'password_confirmation',
            'location_image',
            'business_legality_photo_data',
            'business_detail_1_photo_data',
            'business_detail_2_photo_data',
            'collaterals.*.images_data',
            'collaterals.*.images_data.*',
        ]);
    })->create();
