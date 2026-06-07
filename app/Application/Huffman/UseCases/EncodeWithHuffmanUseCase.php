<?php

namespace App\Application\Huffman\UseCases;

use App\Application\Huffman\Ports\EncodeWithHuffmanPort;
use App\Domain\Huffman\HuffmanEncoder;
use App\Domain\Huffman\HuffmanResult;
use App\Domain\Huffman\HuffmanTree;
use App\Domain\Shared\ValueObjects\CompressionStats;
use App\Domain\Shared\ValueObjects\FrequencyTable;

final class EncodeWithHuffmanUseCase implements EncodeWithHuffmanPort
{
    private readonly HuffmanTree $treeBuilder;
    private readonly HuffmanEncoder $encoder;

    public function __construct()
    {
        $this->treeBuilder = new HuffmanTree();
        $this->encoder     = new HuffmanEncoder();
    }

    public function execute(string $text): HuffmanResult
    {
        $frequencies = FrequencyTable::fromText($text);
        $tree        = $this->treeBuilder->build($frequencies);
        $codeTable   = $this->encoder->assignCodes($tree);
        $stats       = CompressionStats::fromText($text, $codeTable, $frequencies);
        $encoded     = $codeTable->encode($text);

        return new HuffmanResult($frequencies, $tree, $codeTable, $stats, $encoded);
    }
}
