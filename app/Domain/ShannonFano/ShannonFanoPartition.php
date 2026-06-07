<?php

namespace App\Domain\ShannonFano;

final class ShannonFanoPartition
{
    /**
     * Finds the index that splits symbols[start..end] into two groups with
     * the most equal frequency sums. Returns the last index of the upper group.
     *
     * @param array<string> $symbols Symbols sorted descending by frequency
     * @param array<string, int> $frequencies
     */
    public function findSplit(array $symbols, int $start, int $end, array $frequencies): int
    {
        $total     = 0;
        for ($i = $start; $i <= $end; $i++) {
            $total += $frequencies[$symbols[$i]];
        }

        $upperSum = 0;
        $bestSplit = $start;
        $bestDiff  = PHP_INT_MAX;

        for ($i = $start; $i < $end; $i++) {
            $upperSum += $frequencies[$symbols[$i]];
            $lowerSum  = $total - $upperSum;
            $diff      = abs($upperSum - $lowerSum);
            if ($diff < $bestDiff) {
                $bestDiff  = $diff;
                $bestSplit = $i;
            }
        }

        return $bestSplit;
    }
}
