<?php

use App\Domain\Huffman\HuffmanEncoder;
use App\Domain\Huffman\HuffmanTree;
use App\Domain\Shared\ValueObjects\FrequencyTable;

test('root frequency equals total character count', function (): void {
    $frequencies = FrequencyTable::fromText('abracadabra');
    $tree        = (new HuffmanTree())->build($frequencies);

    expect($tree->frequency)->toBe(11);
});

test('every internal node has exactly two children', function (): void {
    $frequencies = FrequencyTable::fromText('abracadabra');
    $tree        = (new HuffmanTree())->build($frequencies);

    $checkInternals = function ($node) use (&$checkInternals): void {
        if ($node->isLeaf()) {
            return;
        }
        expect($node->left)->not->toBeNull()
            ->and($node->right)->not->toBeNull();
        $checkInternals($node->left);
        $checkInternals($node->right);
    };
    $checkInternals($tree);
});

test('codes are prefix-free', function (): void {
    $frequencies = FrequencyTable::fromText('abracadabra');
    $tree        = (new HuffmanTree())->build($frequencies);
    $codeTable   = (new HuffmanEncoder())->assignCodes($tree);
    $codes       = array_values($codeTable->entries());

    foreach ($codes as $i => $codeA) {
        foreach ($codes as $j => $codeB) {
            if ($i === $j) {
                continue;
            }
            expect(str_starts_with($codeB, $codeA))->toBeFalse(
                "Code '{$codeA}' is a prefix of '{$codeB}' — not prefix-free"
            );
        }
    }
});

test('all symbols get assigned a code', function (): void {
    $frequencies = FrequencyTable::fromText('abracadabra');
    $tree        = (new HuffmanTree())->build($frequencies);
    $codeTable   = (new HuffmanEncoder())->assignCodes($tree);

    foreach (array_keys($frequencies->entries()) as $symbol) {
        expect($codeTable->codeFor($symbol))->not->toBeEmpty("Symbol '{$symbol}' has no code");
    }
});
