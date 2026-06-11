<?php

namespace App\MailClient\Application\Port\In;

use App\MailClient\Domain\Model\MailCredentials;

interface AuthenticateUseCase
{
    public function execute(
        string $username,
        string $password,
        string $smtpHost,
        int    $smtpPort,
        string $pop3Host,
        int    $pop3Port,
        bool   $tls,
    ): MailCredentials;
}
