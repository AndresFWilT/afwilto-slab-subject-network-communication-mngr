<?php

namespace App\MailClient\Domain\Model;

final class MailCredentials
{
    public function __construct(
        public readonly string $username,
        public readonly string $encryptedPassword,
        public readonly string $smtpHost,
        public readonly int    $smtpPort,
        public readonly string $pop3Host,
        public readonly int    $pop3Port,
        public readonly string $domain,
        public readonly bool   $tls,
    ) {}

    public function email(): string
    {
        return $this->username . '@' . $this->domain;
    }
}
