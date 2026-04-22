<?php

use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\CheckFullAccess;
use App\Http\Middleware\NoCache;
use App\Listeners\LogAuthenticationActivity;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Event;

// Event::listen(Login::class, [LogAuthenticationActivity::class, 'handle']);
// Event::listen(Logout::class, [LogAuthenticationActivity::class, 'handle']);

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth.token' => AuthMiddleware::class,
            'check.full.access' => CheckFullAccess::class,
            'nocache' => NoCache::class, // tambahkan ini
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
