<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Application\Huffman\Ports\EncodeWithHuffmanPort;
use App\Application\Huffman\UseCases\EncodeWithHuffmanUseCase;
use App\Application\ShannonFano\Ports\EncodeWithShannonFanoPort;
use App\Application\ShannonFano\UseCases\EncodeWithShannonFanoUseCase;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EncodeWithHuffmanPort::class, EncodeWithHuffmanUseCase::class);
        $this->app->bind(EncodeWithShannonFanoPort::class, EncodeWithShannonFanoUseCase::class);
    }

    public function boot(): void {}
}
