<?php

use App\Http\Middleware\AdminMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'admin' => AdminMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (Throwable $exception, $request) {

            if ($exception instanceof AuthenticationException && $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                    'data' => null,
                ], 401);
            }

            if ($exception instanceof NotFoundHttpException && $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not found',
                    'data' => null,
                ], 404);
            }

            if ($exception instanceof MethodNotAllowedHttpException && $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'method is not supported.',
                    'data' => null,
                ], 404);
            }
        });
    })->create();
