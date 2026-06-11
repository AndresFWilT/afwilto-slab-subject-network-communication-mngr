<?php

namespace App\MailClient\Application\UseCase;

use App\MailClient\Application\Port\In\AuthenticateUseCase;
use App\MailClient\Application\Port\Out\CredentialEncryptor;
use App\MailClient\Application\Port\Out\MailRetriever;
use App\MailClient\Domain\Model\MailCredentials;

final class AuthenticateService implements AuthenticateUseCase
{
    private MailRetriever $retriever;
    private CredentialEncryptor $encryptor;

    public function __construct(MailRetriever $retriever, CredentialEncryptor $encryptor)
    {
        $this->retriever = $retriever;
        $this->encryptor = $encryptor;
    }

    public function execute(
        string $username,
        string $password,
        string $smtpHost,
        int    $smtpPort,
        string $pop3Host,
        int    $pop3Port,
        bool   $tls,
    ): MailCredentials {
        $domain = config('mail-client.domain', 'localhost');

        $this->retriever->authenticate($username, $password, $pop3Host, $pop3Port, $tls);

        $encryptedPassword = $this->encryptor->encrypt($password);

        return new MailCredentials(
            username: $username,
            encryptedPassword: $encryptedPassword,
            smtpHost: $smtpHost,
            smtpPort: $smtpPort,
            pop3Host: $pop3Host,
            pop3Port: $pop3Port,
            domain: $domain,
            tls: $tls,
        );
    }
}
