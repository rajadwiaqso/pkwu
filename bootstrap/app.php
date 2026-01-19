<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

return Application::configure(basePath: dirname(__DIR__))


    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo('/');
        

         $middleware->web(append: [
        HandleInertiaRequests::class,
    ]);
      
        // API middleware for Sanctum
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);

      $middleware->append(\App\Http\Middleware\RedirectMethodNotAllowed::class);


        // Register role-based middleware aliases
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
            'permission' => \App\Http\Middleware\PermissionMiddleware::class,
            'under-development' => \App\Http\Middleware\UnderDevelopmentMiddleware::class,
            'verified_email' => \App\Http\Middleware\EnsureEmailVerified::class,
        ]);

        $middleware->validateCsrfTokens(except: [
        'midtrans-notification', // endpoint notifikasi Midtrans
        // tambahkan endpoint lain jika perlu
        'tripay-callback', // endpoint notifikasi Tripay
    ]);
    })

    ->withExceptions(function (Exceptions $exceptions) {
    // $exceptions->render(
    //     fn (MethodNotAllowedHttpException $e, $request) => redirect()->route('home')
    // );
})
    
    ->create();
