<?php

namespace App\Application\Huffman\Ports;

use App\Domain\Huffman\HuffmanResult;

interface EncodeWithHuffmanPort
{
    public function execute(string $text): HuffmanResult;
}
