<?php

namespace App\MailClient\Adapter\In\Http\Middleware;

use App\MailClient\Application\Port\Out\CredentialEncryptor;
use App\MailClient\Domain\Error\MailClientException;
use App\MailClient\Domain\Model\MailCredentials;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class MailAuthMiddleware
{
    private CredentialEncryptor $encryptor;

    public function __construct(CredentialEncryptor $encryptor)
    {
        $this->encryptor = $encryptor;
    }

    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization', '');
        if (!str_starts_with($header, 'Bearer ')) {
            return response()->json(
                ['error' => ['code' => MailClientException::INVALID_TOKEN, 'message' => 'Missing Bearer token.']],
                401
            );
        }

        $token  = substr($header, 7);
        $secret = $this->jwtSecret();

        try {
            $claims = JwtHelper::decode($token, $secret);
        } catch (MailClientException $e) {
            $status = $e->errorCode() === MailClientException::TOKEN_EXPIRED ? 401 : 401;
            return response()->json(
                ['error' => ['code' => $e->errorCode(), 'message' => $e->getMessage()]],
                $status
            );
        }

        $server = $claims['server'] ?? [];
        $credentials = new MailCredentials(
            username:          $claims['sub'] ?? '',
            encryptedPassword: $claims['cred'] ?? '',
            smtpHost:          $server['smtpHost'] ?? 'localhost',
            smtpPort:          (int) ($server['smtpPort'] ?? 25),
            pop3Host:          $server['pop3Host'] ?? 'localhost',
            pop3Port:          (int) ($server['pop3Port'] ?? 110),
            domain:            $server['domain'] ?? 'localhost',
            tls:               (bool) ($server['tls'] ?? false),
        );

        $request->attributes->set('mailCredentials', $credentials);

        return $next($request);
    }

    private function jwtSecret(): string
    {
        $key = config('app.key', '');
        return str_starts_with($key, 'base64:')
            ? base64_decode(substr($key, 7))
            : $key;
    }
}
