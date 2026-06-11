<?php

use App\Adapters\In\Http\Controllers\HuffmanController;
use App\Adapters\In\Http\Controllers\ShannonFanoController;
use App\WeatherStation\Adapter\In\Http\WeatherStationController;
use App\MailClient\Adapter\In\Http\MailClientController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/huffman/encode',      [HuffmanController::class, 'encode']);
    Route::post('/shannon-fano/encode', [ShannonFanoController::class, 'encode']);

    Route::prefix('weather-station')->group(function (): void {
        Route::post('/readings',         [WeatherStationController::class, 'ingest']);
        Route::get('/readings/latest',   [WeatherStationController::class, 'latest']);
        Route::get('/readings/summary',  [WeatherStationController::class, 'summary']);
        Route::get('/readings',          [WeatherStationController::class, 'queryReadings']);
    });

    Route::prefix('mail')->group(function (): void {
        Route::post('/authenticate',           [MailClientController::class, 'authenticate']);

        Route::middleware('mail.auth')->group(function (): void {
            Route::get('/messages',            [MailClientController::class, 'listMessages']);
            Route::post('/messages/send',      [MailClientController::class, 'sendMessage']);
            Route::get('/messages/{id}',       [MailClientController::class, 'getMessage']);
        });
    });
});
