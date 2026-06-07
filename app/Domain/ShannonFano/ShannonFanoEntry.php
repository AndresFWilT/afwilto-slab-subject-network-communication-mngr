<?php

namespace App\Domain\ShannonFano;

final class ShannonFanoEntry
{
    public readonly string $symbol;
    public readonly int $frequency;
    public readonly float $probability;
    public readonly float $entropy;
    public readonly float $messageEntropy;
    public readonly int $codeBitLength;
    public readonly int $messageBits;

    public function __construct(
        string $symbol,
        int $frequency,
        int $total,
        string $code,
    ) {
        $this->symbol        = $symbol;
        $this->frequency     = $frequency;
        $this->probability   = $total > 0 ? round($frequency / $total, 3) : 0.0;
        // Self-information: -log2(p) = log2(total/frequency)
        $this->entropy       = $frequency > 0 ? round(log($total / $frequency, 2), 3) : 0.0;
        $this->messageEntropy = round($this->entropy * $frequency, 3);
        $this->codeBitLength = strlen($code);
        $this->messageBits   = $this->codeBitLength * $frequency;
    }
}
