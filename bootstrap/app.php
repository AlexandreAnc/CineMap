<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsSubscribed;
use App\Http\Middleware\ValidateMcpReadToken;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->preventRequestForgery(except: [
            'stripe/*',
        ]);
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'mcp.read' => ValidateMcpReadToken::class,
            'subscribed' => EnsureUserIsSubscribed::class,
        ]);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('locations:prune-stale')->daily();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
