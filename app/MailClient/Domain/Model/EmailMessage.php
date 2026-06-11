<?php

namespace App\MailClient\Domain\Model;

final class EmailMessage
{
    public function __construct(
        public readonly int $id,
        public readonly string $from,
        public readonly string $to,
        public readonly string $subject,
        public readonly string $date,
        public readonly string $body,
        public readonly string $bodyPreview,
    ) {}

    public static function create(
        int $id,
        string $from,
        string $to,
        string $subject,
        string $date,
        string $body,
    ): self {
        return new self(
            id: $id,
            from: $from,
            to: $to,
            subject: $subject ?: '(no subject)',
            date: $date,
            body: $body,
            bodyPreview: mb_substr(trim($body), 0, 200),
        );
    }
}
