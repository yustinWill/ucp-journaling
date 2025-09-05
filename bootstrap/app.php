<?php

use App\Enums\ErrorCodes;
use Illuminate\Auth\AuthenticationException;
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
            'role.permission' => \App\Http\Middleware\RolePermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => ErrorCodes::NOT_AUTHORIZED->message(),
                    'error' => ErrorCodes::NOT_AUTHORIZED->code(),
                ], ErrorCodes::NOT_AUTHORIZED->httpStatus());
            }

            return redirect()->guest(route('login'));
        });
    })
    ->create();
