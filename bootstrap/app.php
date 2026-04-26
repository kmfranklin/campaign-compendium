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
            'admin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        ]);

        // Run on every web request. Boots suspended users out immediately
        // rather than waiting until they try to log in again.
        $middleware->appendToGroup('web', \App\Http\Middleware\CheckSuspended::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
