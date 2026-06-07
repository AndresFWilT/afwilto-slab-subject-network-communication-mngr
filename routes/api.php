<?php

use App\Adapters\In\Http\Controllers\HuffmanController;
use App\Adapters\In\Http\Controllers\ShannonFanoController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::post('/huffman/encode',      [HuffmanController::class, 'encode']);
    Route::post('/shannon-fano/encode', [ShannonFanoController::class, 'encode']);
});
