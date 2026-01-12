<?php

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
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);

    $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
})

    ->withExceptions(function (Exceptions $exceptions): void {
    $exceptions->render(function (\Illuminate\Validation\ValidationException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], $e->status);
    });

    $exceptions->render(function (\Illuminate\Auth\AuthenticationException $e, $request) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated',
        ], 401);
    });

    $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
        if ($e->getStatusCode() === 403) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden',
            ], 403);
        }
    });
    })->create();
