<?php

namespace App\Domain\ShannonFano;

use App\Domain\Shared\ValueObjects\CodeTable;
use App\Domain\Shared\ValueObjects\FrequencyTable;

final class ShannonFanoEncoder
{
    private readonly ShannonFanoPartition $partition;

    public function __construct()
    {
        $this->partition = new ShannonFanoPartition();
    }

    public function assignCodes(FrequencyTable $frequencies): CodeTable
    {
        $entries   = $frequencies->entries(); // already sorted descending by frequency
        $symbols   = array_keys($entries);
        $freqMap   = $entries;
        $codes     = [];

        $this->recurse($symbols, 0, count($symbols) - 1, '', $freqMap, $codes);

        return new CodeTable($codes);
    }

    /**
     * Recursive Shannon-Fano partitioning — correctly assigns codes to ALL symbols.
     * Fixes the legacy bug where the lower half got an empty loop body.
     *
     * @param array<string>      $symbols
     * @param array<string, int> $frequencies
     * @param array<string, string> $codes (output)
     */
    private function recurse(
        array $symbols,
        int $start,
        int $end,
        string $prefix,
        array $frequencies,
        array &$codes,
    ): void {
        if ($start === $end) {
            $codes[$symbols[$start]] = $prefix === '' ? '0' : $prefix;
            return;
        }

        $split = $this->partition->findSplit($symbols, $start, $end, $frequencies);

        $this->recurse($symbols, $start, $split, $prefix . '0', $frequencies, $codes);
        $this->recurse($symbols, $split + 1, $end, $prefix . '1', $frequencies, $codes);
    }
}
