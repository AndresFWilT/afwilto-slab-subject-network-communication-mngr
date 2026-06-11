<?php

namespace App\MailClient\Domain\Model;

final class AuthResult
{
    public function __construct(
        public readonly string $token,
        public readonly string $username,
        public readonly string $email,
        public readonly \DateTimeImmutable $expiresAt,
    ) {}
}
