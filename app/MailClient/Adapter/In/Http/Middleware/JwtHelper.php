<?php

namespace App\MailClient\Adapter\In\Http\Middleware;

use App\MailClient\Domain\Error\MailClientException;

final class JwtHelper
{
    public static function encode(array $payload, string $secret): string
    {
        $header  = self::b64u(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body    = self::b64u(json_encode($payload));
        $sig     = self::b64u(hash_hmac('sha256', "{$header}.{$body}", $secret, true));
        return "{$header}.{$body}.{$sig}";
    }

    public static function decode(string $token, string $secret): array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw MailClientException::invalidToken('Malformed token.');
        }

        [$header, $body, $sig] = $parts;
        $expected = self::b64u(hash_hmac('sha256', "{$header}.{$body}", $secret, true));

        if (!hash_equals($expected, $sig)) {
            throw MailClientException::invalidToken('Token signature invalid.');
        }

        $payload = json_decode(self::b64uDecode($body), true);
        if (!is_array($payload)) {
            throw MailClientException::invalidToken('Token payload corrupt.');
        }

        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw MailClientException::tokenExpired();
        }

        return $payload;
    }

    private static function b64u(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function b64uDecode(string $data): string
    {
        return base64_decode(strtr($data, '-_', '+/') . str_repeat('=', (4 - strlen($data) % 4) % 4));
    }
}
