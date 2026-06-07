<?php

namespace App\Domain\Shared\ValueObjects;

final class CodeTable
{
    /** @var array<string, string> symbol → binary code string */
    private readonly array $codes;

    public function __construct(array $codes)
    {
        $this->codes = $codes;
    }

    public function codeFor(string $symbol): string
    {
        return $this->codes[$symbol] ?? '';
    }

    /** @return array<string, string> */
    public function entries(): array
    {
        return $this->codes;
    }

    public function encode(string $text): string
    {
        $bits = '';
        $length = mb_strlen($text);
        for ($i = 0; $i < $length; $i++) {
            $bits .= $this->codes[mb_substr($text, $i, 1)] ?? '';
        }
        return $bits;
    }

    public function compressedBitCount(FrequencyTable $frequencies): int
    {
        $total = 0;
        foreach ($frequencies->entries() as $symbol => $count) {
            $total += strlen($this->codes[$symbol] ?? '') * $count;
        }
        return $total;
    }
}
