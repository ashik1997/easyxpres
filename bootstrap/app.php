<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\ApprovedMiddleware;
use App\Http\Middleware\Languages;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
         $middleware->use([
            Languages::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'balance/eps/ipn',
            'api/shop/wallet/*',
        ]);
        $middleware->alias([
            'admin'    => AdminMiddleware::class,
            'approved' => ApprovedMiddleware::class,
            'user'     => \App\Http\Middleware\UserMiddleware::class,
            'admin.auth' => \App\Http\Middleware\AdminAuth::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
