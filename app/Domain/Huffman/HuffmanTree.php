<?php

namespace App\Domain\Huffman;

use App\Domain\Shared\ValueObjects\FrequencyTable;

final class HuffmanTree
{
    public function build(FrequencyTable $frequencies): HuffmanNode
    {
        $heap = new HuffmanMinHeap();

        foreach ($frequencies->entries() as $symbol => $freq) {
            $heap->insert(HuffmanNode::leaf($symbol, $freq));
        }

        while ($heap->count() > 1) {
            $left  = $heap->extract();
            $right = $heap->extract();
            $heap->insert(HuffmanNode::internal($left, $right));
        }

        return $heap->extract();
    }
}

/**
 * Min-heap ordered by frequency.
 * Tie-breaking: lower frequency wins; among equal, leaf before internal; alphabetical by symbol.
 */
final class HuffmanMinHeap extends \SplMinHeap
{
    protected function compare($a, $b): int
    {
        // SplMinHeap::compare(a,b): positive means a > b (b extracted first = b is smaller)
        if ($a->frequency !== $b->frequency) {
            return $a->frequency - $b->frequency;
        }
        $aIsLeaf = $a->isLeaf() ? 1 : 0;
        $bIsLeaf = $b->isLeaf() ? 1 : 0;
        if ($aIsLeaf !== $bIsLeaf) {
            // leaf (1) < internal (0) → leaf is "smaller" in min-heap → extracted first
            return $bIsLeaf - $aIsLeaf;
        }
        return strcmp($a->symbol ?? '', $b->symbol ?? '');
    }
}
