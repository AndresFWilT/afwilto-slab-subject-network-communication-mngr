<?php

namespace App\Application\ShannonFano\UseCases;

use App\Application\ShannonFano\Ports\EncodeWithShannonFanoPort;
use App\Domain\ShannonFano\ShannonFanoEncoder;
use App\Domain\ShannonFano\ShannonFanoResult;
use App\Domain\Shared\ValueObjects\CompressionStats;
use App\Domain\Shared\ValueObjects\FrequencyTable;

final class EncodeWithShannonFanoUseCase implements EncodeWithShannonFanoPort
{
    private readonly ShannonFanoEncoder $encoder;

    public function __construct()
    {
        $this->encoder = new ShannonFanoEncoder();
    }

    public function execute(string $text): ShannonFanoResult
    {
        $frequencies = FrequencyTable::fromText($text);
        $codeTable   = $this->encoder->assignCodes($frequencies);
        $stats       = CompressionStats::fromText($text, $codeTable, $frequencies);
        $encoded     = $codeTable->encode($text);

        return new ShannonFanoResult($frequencies, $codeTable, $stats, $encoded);
    }
}
