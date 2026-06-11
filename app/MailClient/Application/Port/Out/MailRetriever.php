<?php

namespace App\MailClient\Application\Port\Out;

use App\MailClient\Domain\Model\EmailMessage;
use App\MailClient\Domain\Model\Mailbox;

interface MailRetriever
{
    public function authenticate(
        string $username,
        string $password,
        string $host,
        int    $port,
        bool   $tls,
    ): Mailbox;

    /** @return EmailMessage[] */
    public function listMessages(
        string $username,
        string $password,
        string $host,
        int    $port,
        bool   $tls,
    ): array;

    public function getMessage(
        string $username,
        string $password,
        string $host,
        int    $port,
        bool   $tls,
        int    $id,
    ): EmailMessage;
}
