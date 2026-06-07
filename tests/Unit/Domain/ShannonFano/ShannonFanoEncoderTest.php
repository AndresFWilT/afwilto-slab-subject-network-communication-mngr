<?php

use App\Domain\ShannonFano\ShannonFanoEncoder;
use App\Domain\ShannonFano\ShannonFanoPartition;
use App\Domain\Shared\ValueObjects\FrequencyTable;

test('partition finds optimal split for abracadabra frequencies', function (): void {
    // Sorted descending: a=5, b=2, r=2, c=1, d=1
    $symbols    = ['a', 'b', 'r', 'c', 'd'];
    $frequencies = ['a' => 5, 'b' => 2, 'r' => 2, 'c' => 1, 'd' => 1];
    $partition  = new ShannonFanoPartition();

    $split = $partition->findSplit($symbols, 0, 4, $frequencies);

    // Upper=[a]=5, Lower=[b,r,c,d]=6, diff=1 (smallest possible)
    expect($split)->toBe(0);
});

test('all symbols get codes with Shannon-Fano', function (): void {
    $frequencies = FrequencyTable::fromText('abracadabra');
    $codeTable   = (new ShannonFanoEncoder())->assignCodes($frequencies);

    foreach (array_keys($frequencies->entries()) as $symbol) {
        expect($codeTable->codeFor($symbol))->not->toBeEmpty(
            "Shannon-Fano: symbol '{$symbol}' has no code (legacy bug — both halves must be covered)"
        );
    }
});

test('Shannon-Fano codes are prefix-free', function (): void {
    $frequencies = FrequencyTable::fromText('abracadabra');
    $codeTable   = (new ShannonFanoEncoder())->assignCodes($frequencies);
    $codes       = array_values($codeTable->entries());

    foreach ($codes as $i => $codeA) {
        foreach ($codes as $j => $codeB) {
            if ($i === $j) {
                continue;
            }
            expect(str_starts_with($codeB, $codeA))->toBeFalse(
                "Shannon-Fano code '{$codeA}' is a prefix of '{$codeB}'"
            );
        }
    }
});

test('compression stats are correct for abracadabra', function (): void {
    $frequencies = FrequencyTable::fromText('abracadabra');
    $encoder     = new ShannonFanoEncoder();
    $codeTable   = $encoder->assignCodes($frequencies);

    $originalBits   = 11 * 8; // 88
    $compressedBits = $codeTable->compressedBitCount($frequencies);

    // Savings should be substantial (similar to Huffman ~74%)
    $savings = round((($originalBits - $compressedBits) / $originalBits) * 100, 1);

    expect($savings)->toBeGreaterThan(60.0)
        ->and($compressedBits)->toBeLessThan($originalBits);
});
