<?php

namespace App\MailClient\Application\UseCase;

use App\MailClient\Application\Port\In\GetMessageUseCase;
use App\MailClient\Application\Port\Out\CredentialEncryptor;
use App\MailClient\Application\Port\Out\MailRetriever;
use App\MailClient\Domain\Model\EmailMessage;
use App\MailClient\Domain\Model\MailCredentials;

final class GetMessageService implements GetMessageUseCase
{
    private MailRetriever $retriever;
    private CredentialEncryptor $encryptor;

    public function __construct(MailRetriever $retriever, CredentialEncryptor $encryptor)
    {
        $this->retriever = $retriever;
        $this->encryptor = $encryptor;
    }

    public function execute(MailCredentials $credentials, int $id): EmailMessage
    {
        $plainPassword = $this->encryptor->decrypt($credentials->encryptedPassword);

        return $this->retriever->getMessage(
            $credentials->username,
            $plainPassword,
            $credentials->pop3Host,
            $credentials->pop3Port,
            $credentials->tls,
            $id,
        );
    }
}
