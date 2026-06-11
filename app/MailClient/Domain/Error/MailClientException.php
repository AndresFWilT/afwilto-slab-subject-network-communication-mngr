<?php

namespace App\MailClient\Domain\Error;

final class MailClientException extends \RuntimeException
{
    public const AUTH_FAILED       = 'AUTH_FAILED';
    public const SMTP_ERROR        = 'SMTP_ERROR';
    public const POP3_ERROR        = 'POP3_ERROR';
    public const TOKEN_EXPIRED     = 'TOKEN_EXPIRED';
    public const INVALID_TOKEN     = 'INVALID_TOKEN';
    public const INVALID_RECIPIENT = 'INVALID_RECIPIENT';

    private function __construct(
        private readonly string $errorCode,
        string $message,
        ?\Throwable $previous = null,
    ) {
        parent::__construct($message, 0, $previous);
    }

    public static function authFailed(string $reason = 'Authentication failed'): self
    {
        return new self(self::AUTH_FAILED, $reason);
    }

    public static function smtpError(string $reason, ?\Throwable $previous = null): self
    {
        return new self(self::SMTP_ERROR, "SMTP error: {$reason}", $previous);
    }

    public static function pop3Error(string $reason, ?\Throwable $previous = null): self
    {
        return new self(self::POP3_ERROR, "POP3 error: {$reason}", $previous);
    }

    public static function tokenExpired(): self
    {
        return new self(self::TOKEN_EXPIRED, 'Authentication token has expired.');
    }

    public static function invalidToken(string $reason = 'Token is invalid'): self
    {
        return new self(self::INVALID_TOKEN, $reason);
    }

    public static function invalidRecipient(string $email): self
    {
        return new self(self::INVALID_RECIPIENT, "Invalid recipient email: {$email}");
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
