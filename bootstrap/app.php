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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
        'pharmacy.associated' => \App\Http\Middleware\CheckPharmacyAssociation::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'auth' => \App\Http\Middleware\Authenticate::class,
        'admin' => \App\Http\Middleware\CheckAdmin::class,
        'super.admin' => \App\Http\Middleware\CheckSuperAdmin::class,
        'pharmacy.associated' => \App\Http\Middleware\CheckPharmacyAssociation::class,
          ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
