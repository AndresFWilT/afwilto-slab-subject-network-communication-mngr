<?php

namespace App\MailClient\Application\Port\In;

use App\MailClient\Domain\Model\MailCredentials;

interface SendMessageUseCase
{
    public function execute(
        MailCredentials $credentials,
        string $to,
        string $subject,
        string $body,
    ): void;
}
