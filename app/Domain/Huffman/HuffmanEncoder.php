<?php

namespace App\Domain\Huffman;

use App\Domain\Shared\ValueObjects\CodeTable;

final class HuffmanEncoder
{
    public function assignCodes(HuffmanNode $root): CodeTable
    {
        $codes = [];
        $this->traverse($root, '', $codes);
        return new CodeTable($codes);
    }

    private function traverse(HuffmanNode $node, string $prefix, array &$codes): void
    {
        if ($node->isLeaf()) {
            // Single-character edge case: tree has exactly one node
            $codes[$node->symbol] = $prefix === '' ? '0' : $prefix;
            return;
        }
        if ($node->left !== null) {
            $this->traverse($node->left, $prefix . '0', $codes);
        }
        if ($node->right !== null) {
            $this->traverse($node->right, $prefix . '1', $codes);
        }
    }
}
