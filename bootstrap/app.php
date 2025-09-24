<?php

use App\Http\Middleware\AdminJwtAuthRedirect;
use App\Http\Middleware\CheckRefreshToken;
use App\Http\Middleware\CheckUserPermission;
use App\Http\Middleware\UserJwtAuthRedirect;
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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'check.jwt' => UserJwtAuthRedirect::class,
            'url_permission' => CheckUserPermission::class,
            'check.refresh.tkn'=>CheckRefreshToken::class 
        ]);
        $middleware->encryptCookies(except: [
            'token',
        ]);
    })
    ->withSchedule(function(Schedule $schedule){
        $schedule->command('token:cleanup')->dailyAt('6:00');
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
