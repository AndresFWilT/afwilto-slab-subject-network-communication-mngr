<?php

namespace App\MailClient\Application\UseCase;

use App\MailClient\Application\Port\In\ListMessagesUseCase;
use App\MailClient\Application\Port\Out\CredentialEncryptor;
use App\MailClient\Application\Port\Out\MailRetriever;
use App\MailClient\Domain\Model\MailCredentials;

final class ListMessagesService implements ListMessagesUseCase
{
    private MailRetriever $retriever;
    private CredentialEncryptor $encryptor;

    public function __construct(MailRetriever $retriever, CredentialEncryptor $encryptor)
    {
        $this->retriever = $retriever;
        $this->encryptor = $encryptor;
    }

    public function execute(MailCredentials $credentials): array
    {
        $plainPassword = $this->encryptor->decrypt($credentials->encryptedPassword);

        $messages = $this->retriever->listMessages(
            $credentials->username,
            $plainPassword,
            $credentials->pop3Host,
            $credentials->pop3Port,
            $credentials->tls,
        );

        return ['messages' => $messages, 'totalCount' => count($messages)];
    }
}
