<?php

use App\Http\Middleware\Admin;
use App\Http\Middleware\APIGuardMiddleware;
use App\Http\Middleware\InstalledMiddleware;
use App\Http\Middleware\Locale;
use App\Http\Middleware\PrepareInstallation;
use App\Http\Middleware\SettingsMiddleware;
use App\Http\Middleware\VerifyPaymentEnabled;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(PrepareInstallation::class);
        $middleware->append(SettingsMiddleware::class);
        $middleware->encryptCookies(except: [
            'dark_mode',
            'cookie_law',
        ]);
        $middleware->validateCsrfTokens(except: [
            'stripe/*',
        ]);
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('home'));

        $middleware->web(append: [
            Locale::class,
        ]);

        $middleware->api(append: [
            Locale::class,
        ]);

        $middleware->alias([
            'admin' => Admin::class,
            'api.guard' => APIGuardMiddleware::class,
            'installed' => InstalledMiddleware::class,
            'payment' => VerifyPaymentEnabled::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
