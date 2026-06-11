<?php

namespace App\MailClient\Application\Port\In;

use App\MailClient\Domain\Model\EmailMessage;
use App\MailClient\Domain\Model\MailCredentials;

interface GetMessageUseCase
{
    public function execute(MailCredentials $credentials, int $id): EmailMessage;
}
