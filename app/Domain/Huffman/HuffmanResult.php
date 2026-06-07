<?php

namespace App\Domain\Huffman;

use App\Domain\Shared\ValueObjects\CodeTable;
use App\Domain\Shared\ValueObjects\CompressionStats;
use App\Domain\Shared\ValueObjects\FrequencyTable;

final class HuffmanResult
{
    public readonly FrequencyTable $frequencies;
    public readonly HuffmanNode $tree;
    public readonly CodeTable $codeTable;
    public readonly CompressionStats $stats;
    public readonly string $encodedBitString;

    public function __construct(
        FrequencyTable $frequencies,
        HuffmanNode $tree,
        CodeTable $codeTable,
        CompressionStats $stats,
        string $encodedBitString,
    ) {
        $this->frequencies      = $frequencies;
        $this->tree             = $tree;
        $this->codeTable        = $codeTable;
        $this->stats            = $stats;
        $this->encodedBitString = $encodedBitString;
    }
}
