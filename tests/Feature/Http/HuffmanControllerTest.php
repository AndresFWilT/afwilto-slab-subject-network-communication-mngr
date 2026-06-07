<?php

use Illuminate\Testing\Fluent\AssertableJson;

test('POST /api/v1/huffman/encode returns correct structure for abracadabra', function (): void {
    $response = $this->postJson('/api/v1/huffman/encode', ['text' => 'abracadabra']);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'frequencyTable' => [['symbol', 'frequency']],
                'tree'           => ['frequency', 'symbol', 'left', 'right'],
                'codeTable'      => [['symbol', 'code', 'bitLength']],
                'encoding'       => [
                    'encodedBitString',
                    'originalBitCount',
                    'compressedBitCount',
                    'compressionRatio',
                    'savingsPercent',
                ],
            ],
        ]);

    $response->assertJson(fn(AssertableJson $json) =>
        $json->where('data.encoding.originalBitCount', 88)
             ->where('data.encoding.compressedBitCount', fn($v) => $v < 88)
             ->etc()
    );
});

test('POST /api/v1/huffman/encode returns 400 for empty text', function (): void {
    $response = $this->postJson('/api/v1/huffman/encode', ['text' => '']);

    $response->assertStatus(400)
        ->assertJsonPath('error.code', 'EMPTY_INPUT');
});

test('POST /api/v1/huffman/encode returns 400 for single distinct character', function (): void {
    $response = $this->postJson('/api/v1/huffman/encode', ['text' => 'aaaa']);

    $response->assertStatus(400)
        ->assertJsonPath('error.code', 'SINGLE_CHARACTER');
});

test('POST /api/v1/huffman/encode returns 422 when text field is missing', function (): void {
    $this->postJson('/api/v1/huffman/encode', [])->assertStatus(422);
});
