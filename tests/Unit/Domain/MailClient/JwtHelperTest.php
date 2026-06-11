<?php

use App\MailClient\Adapter\In\Http\Middleware\JwtHelper;
use App\MailClient\Domain\Error\MailClientException;

test('encode and decode round-trips the payload', function (): void {
    $secret  = 'test-secret-key-32-chars-abcdefg';
    $payload = ['sub' => 'user01', 'exp' => time() + 3600, 'data' => 'value'];

    $token   = JwtHelper::encode($payload, $secret);
    $decoded = JwtHelper::decode($token, $secret);

    expect($decoded['sub'])->toBe('user01')
        ->and($decoded['data'])->toBe('value');
});

test('decode throws on invalid signature', function (): void {
    $secret = 'secret-key';
    $token  = JwtHelper::encode(['sub' => 'user', 'exp' => time() + 3600], $secret);
    $tampered = substr($token, 0, -5) . 'XXXXX';

    expect(fn() => JwtHelper::decode($tampered, $secret))
        ->toThrow(MailClientException::class);
});

test('decode throws when token is expired', function (): void {
    $secret  = 'secret-key';
    $token   = JwtHelper::encode(['sub' => 'user', 'exp' => time() - 1], $secret);

    expect(fn() => JwtHelper::decode($token, $secret))
        ->toThrow(MailClientException::class);
});

test('decode throws on malformed token', function (): void {
    expect(fn() => JwtHelper::decode('not.a.valid.token.structure', 'secret'))
        ->toThrow(MailClientException::class);
});
