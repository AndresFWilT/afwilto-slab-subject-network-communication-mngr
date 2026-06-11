<?php

namespace App\MailClient\Application\Port\In;

use App\MailClient\Domain\Model\EmailMessage;
use App\MailClient\Domain\Model\MailCredentials;

interface ListMessagesUseCase
{
    /** @return array{messages: EmailMessage[], totalCount: int} */
    public function execute(MailCredentials $credentials): array;
}
