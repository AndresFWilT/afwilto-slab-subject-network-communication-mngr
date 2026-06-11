<?php

namespace App\MailClient\Application\UseCase;

use App\MailClient\Application\Port\In\SendMessageUseCase;
use App\MailClient\Application\Port\Out\CredentialEncryptor;
use App\MailClient\Application\Port\Out\MailSender;
use App\MailClient\Domain\Error\MailClientException;
use App\MailClient\Domain\Model\MailCredentials;

final class SendMessageService implements SendMessageUseCase
{
    private MailSender $sender;
    private CredentialEncryptor $encryptor;

    public function __construct(MailSender $sender, CredentialEncryptor $encryptor)
    {
        $this->sender    = $sender;
        $this->encryptor = $encryptor;
    }

    public function execute(
        MailCredentials $credentials,
        string $to,
        string $subject,
        string $body,
    ): void {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            throw MailClientException::invalidRecipient($to);
        }

        $this->sender->send(
            from: $credentials->email(),
            to: $to,
            subject: $subject,
            body: $body,
            smtpHost: $credentials->smtpHost,
            smtpPort: $credentials->smtpPort,
            tls: $credentials->tls,
        );
    }
}
