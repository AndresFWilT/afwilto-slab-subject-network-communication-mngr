<?php

namespace App\Domain\ShannonFano;

use App\Domain\Shared\ValueObjects\CodeTable;
use App\Domain\Shared\ValueObjects\CompressionStats;
use App\Domain\Shared\ValueObjects\FrequencyTable;

final class ShannonFanoResult
{
    /** @var ShannonFanoEntry[] */
    public readonly array $entries;
    public readonly ShannonFanoTotals $totals;
    public readonly CodeTable $codeTable;
    public readonly CompressionStats $stats;
    public readonly string $encodedBitString;

    public function __construct(
        FrequencyTable $frequencies,
        CodeTable $codeTable,
        CompressionStats $stats,
        string $encodedBitString,
    ) {
        $this->codeTable        = $codeTable;
        $this->stats            = $stats;
        $this->encodedBitString = $encodedBitString;

        $total   = $frequencies->totalCount();
        $entries = [];
        foreach ($frequencies->entries() as $symbol => $freq) {
            $entries[] = new ShannonFanoEntry($symbol, $freq, $total, $codeTable->codeFor($symbol));
        }
        $this->entries = $entries;
        $this->totals  = ShannonFanoTotals::fromEntries($entries);
    }
}

final class ShannonFanoTotals
{
    public readonly int $frequency;
    public readonly float $probability;
    public readonly float $entropy;
    public readonly float $messageEntropy;
    public readonly int $codeBits;
    public readonly int $messageBits;

    private function __construct(
        int $frequency,
        float $probability,
        float $entropy,
        float $messageEntropy,
        int $codeBits,
        int $messageBits,
    ) {
        $this->frequency     = $frequency;
        $this->probability   = $probability;
        $this->entropy       = $entropy;
        $this->messageEntropy = $messageEntropy;
        $this->codeBits      = $codeBits;
        $this->messageBits   = $messageBits;
    }

    /** @param ShannonFanoEntry[] $entries */
    public static function fromEntries(array $entries): self
    {
        return new self(
            frequency:     array_sum(array_column($entries, 'frequency')),
            probability:   round(array_sum(array_column($entries, 'probability')), 2),
            entropy:       round(array_sum(array_column($entries, 'entropy')), 3),
            messageEntropy: round(array_sum(array_column($entries, 'messageEntropy')), 3),
            codeBits:      array_sum(array_column($entries, 'codeBitLength')),
            messageBits:   array_sum(array_column($entries, 'messageBits')),
        );
    }
}
