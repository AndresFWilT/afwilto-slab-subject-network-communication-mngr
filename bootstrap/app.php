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
        $middleware->alias([
            'mail.auth' => \App\MailClient\Adapter\In\Http\Middleware\MailAuthMiddleware::class,
        ]);
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

        $exceptions->render(function (\App\WeatherStation\Domain\Error\WeatherStationException $e) {
            $status = $e->errorCode() === \App\WeatherStation\Domain\Error\WeatherStationException::INVALID_DATE_RANGE
                ? 400
                : 422;

            return response()->json([
                'error' => ['code' => $e->errorCode(), 'message' => $e->getMessage()],
            ], $status);
        });

        $exceptions->render(function (\App\MailClient\Domain\Error\MailClientException $e) {
            $status = match ($e->errorCode()) {
                \App\MailClient\Domain\Error\MailClientException::AUTH_FAILED,
                \App\MailClient\Domain\Error\MailClientException::TOKEN_EXPIRED,
                \App\MailClient\Domain\Error\MailClientException::INVALID_TOKEN  => 401,
                \App\MailClient\Domain\Error\MailClientException::SMTP_ERROR,
                \App\MailClient\Domain\Error\MailClientException::POP3_ERROR     => 502,
                \App\MailClient\Domain\Error\MailClientException::INVALID_RECIPIENT => 400,
                default => 500,
            };

            return response()->json([
                'error' => ['code' => $e->errorCode(), 'message' => $e->getMessage()],
            ], $status);
        });
    })->create();
