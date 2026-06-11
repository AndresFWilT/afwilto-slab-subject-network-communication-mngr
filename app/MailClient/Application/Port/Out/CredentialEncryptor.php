<?php

namespace App\MailClient\Application\Port\Out;

interface CredentialEncryptor
{
    public function encrypt(string $plaintext): string;
    public function decrypt(string $ciphertext): string;
}
