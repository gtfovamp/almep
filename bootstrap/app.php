<?php

use App\Http\Middleware\AdminAuth;
use App\Http\Middleware\AdminSessionAuth;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register the custom admin token guard under alias 'admin.auth'
        $middleware->alias([
            'admin.auth'    => AdminAuth::class,      // API token (Basic Auth) guard
            'admin.session' => AdminSessionAuth::class, // web panel session guard
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Unified JSON error responses for API requests.
        $exceptions->render(function (\Throwable $e, Request $request) {
            if (! $request->is('api/*') && ! $request->expectsJson()) {
                return null; // default handling
            }

            if ($e instanceof ValidationException) {
                return response()->json([
                    'message' => 'Ошибка валидации.',
                    'errors'  => $e->errors(),
                ], 422);
            }

            if ($e instanceof ModelNotFoundException || $e instanceof NotFoundHttpException) {
                return response()->json(['message' => 'Запись не найдена.'], 404);
            }

            if ($e instanceof AuthenticationException) {
                return response()->json(['message' => 'Требуется авторизация.'], 401);
            }

            if ($e instanceof HttpExceptionInterface) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Ошибка запроса.',
                ], $e->getStatusCode());
            }

            $status = 500;
            $payload = ['message' => 'Внутренняя ошибка сервера.'];
            if (config('app.debug')) {
                $payload['exception'] = get_class($e);
                $payload['error']     = $e->getMessage();
                $payload['file']      = $e->getFile() . ':' . $e->getLine();
            }
            return response()->json($payload, $status);
        });
    })->create();