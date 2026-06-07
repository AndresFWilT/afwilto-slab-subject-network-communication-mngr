<?php

namespace App\Domain\Shared\ValueObjects;

final class CompressionStats
{
    public readonly int $originalBitCount;
    public readonly int $compressedBitCount;
    public readonly float $compressionRatio;
    public readonly float $savingsPercent;

    public function __construct(int $originalBitCount, int $compressedBitCount)
    {
        $this->originalBitCount    = $originalBitCount;
        $this->compressedBitCount  = $compressedBitCount;
        $this->savingsPercent      = $originalBitCount > 0
            ? round((($originalBitCount - $compressedBitCount) / $originalBitCount) * 100, 3)
            : 0.0;
        $this->compressionRatio    = $this->savingsPercent;
    }

    public static function fromText(string $text, CodeTable $codeTable, FrequencyTable $frequencies): self
    {
        $originalBits   = mb_strlen($text) * 8;
        $compressedBits = $codeTable->compressedBitCount($frequencies);
        return new self($originalBits, $compressedBits);
    }
}
