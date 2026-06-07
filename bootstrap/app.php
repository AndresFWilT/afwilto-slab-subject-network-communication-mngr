<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        health: '/api/v1/health',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Stateless API — no session or web middleware needed
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\App\Domain\Shared\Errors\EmptyInputException $e) {
            return response()->json([
                'error' => ['code' => 'EMPTY_INPUT', 'message' => $e->getMessage()],
            ], 400);
        });

        $exceptions->render(function (\App\Domain\Shared\Errors\SingleCharacterException $e) {
            return response()->json([
                'error' => ['code' => 'SINGLE_CHARACTER', 'message' => $e->getMessage()],
            ], 400);
        });
    })->create();
