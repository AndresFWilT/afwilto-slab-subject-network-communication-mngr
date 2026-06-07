<?php

use App\Domain\Shared\Errors\EmptyInputException;
use App\Domain\Shared\Errors\SingleCharacterException;
use App\Domain\Shared\ValueObjects\FrequencyTable;

test('builds correct frequency table for abracadabra', function (): void {
    $table = FrequencyTable::fromText('abracadabra');
    $entries = $table->entries();

    expect($entries['a'])->toBe(5)
        ->and($entries['b'])->toBe(2)
        ->and($entries['r'])->toBe(2)
        ->and($entries['c'])->toBe(1)
        ->and($entries['d'])->toBe(1)
        ->and($table->totalCount())->toBe(11)
        ->and($table->symbolCount())->toBe(5);
});

test('entries are sorted descending by frequency', function (): void {
    $table  = FrequencyTable::fromText('abracadabra');
    $freqs  = array_values($table->entries());

    for ($i = 0; $i < count($freqs) - 1; $i++) {
        expect($freqs[$i])->toBeGreaterThanOrEqual($freqs[$i + 1]);
    }
});

test('throws EmptyInputException for empty string', function (): void {
    FrequencyTable::fromText('');
})->throws(EmptyInputException::class);

test('throws SingleCharacterException for single distinct symbol', function (): void {
    FrequencyTable::fromText('aaaa');
})->throws(SingleCharacterException::class);
