<?php

use Illuminate\Testing\Fluent\AssertableJson;

test('POST /api/v1/shannon-fano/encode returns correct structure for abracadabra', function (): void {
    $response = $this->postJson('/api/v1/shannon-fano/encode', ['text' => 'abracadabra']);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'entries' => [[
                    'symbol', 'frequency', 'probability',
                    'entropy', 'messageEntropy',
                    'codeBitLength', 'messageBits', 'code',
                ]],
                'totals'   => ['frequency', 'probability', 'entropy', 'messageEntropy', 'codeBits', 'messageBits'],
                'codeTable' => [['symbol', 'code', 'bitLength']],
                'encoding'  => [
                    'encodedBitString', 'originalBitCount',
                    'compressedBitCount', 'compressionRatio', 'savingsPercent',
                ],
            ],
        ]);
});

test('Shannon-Fano encodes ALL 5 symbols for abracadabra', function (): void {
    $response = $this->postJson('/api/v1/shannon-fano/encode', ['text' => 'abracadabra']);

    $response->assertOk();
    $data = $response->json('data');

    // Verify all 5 distinct characters have non-empty codes
    expect($data['codeTable'])->toHaveCount(5);
    foreach ($data['codeTable'] as $entry) {
        expect($entry['code'])->not->toBeEmpty(
            "Symbol '{$entry['symbol']}' has empty code — legacy bug not fixed"
        );
    }
});

test('Shannon-Fano totals row has correct frequency sum', function (): void {
    $response = $this->postJson('/api/v1/shannon-fano/encode', ['text' => 'abracadabra']);
    $response->assertOk();

    expect($response->json('data.totals.frequency'))->toBe(11);
});

test('POST /api/v1/shannon-fano/encode returns 400 for empty text', function (): void {
    $this->postJson('/api/v1/shannon-fano/encode', ['text' => ''])
         ->assertStatus(400)
         ->assertJsonPath('error.code', 'EMPTY_INPUT');
});
