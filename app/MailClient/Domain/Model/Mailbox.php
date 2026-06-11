<?php

namespace App\MailClient\Domain\Model;

final class Mailbox
{
    public function __construct(
        public readonly string $username,
        public readonly int $messageCount,
    ) {}
}
