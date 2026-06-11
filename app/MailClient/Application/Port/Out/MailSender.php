<?php

namespace App\MailClient\Application\Port\Out;

interface MailSender
{
    public function send(
        string $from,
        string $to,
        string $subject,
        string $body,
        string $smtpHost,
        int    $smtpPort,
        bool   $tls,
    ): void;
}
