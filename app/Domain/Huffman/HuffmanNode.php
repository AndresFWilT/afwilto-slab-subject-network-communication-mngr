<?php

namespace App\Domain\Huffman;

final class HuffmanNode
{
    public readonly int $frequency;
    public readonly ?string $symbol;
    public readonly ?HuffmanNode $left;
    public readonly ?HuffmanNode $right;

    private function __construct(int $frequency, ?string $symbol, ?HuffmanNode $left, ?HuffmanNode $right)
    {
        $this->frequency = $frequency;
        $this->symbol    = $symbol;
        $this->left      = $left;
        $this->right     = $right;
    }

    public static function leaf(string $symbol, int $frequency): self
    {
        return new self($frequency, $symbol, null, null);
    }

    public static function internal(HuffmanNode $left, HuffmanNode $right): self
    {
        return new self($left->frequency + $right->frequency, null, $left, $right);
    }

    public function isLeaf(): bool
    {
        return $this->symbol !== null;
    }
}
