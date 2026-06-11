<?php

namespace App\MailClient\Adapter\Out\Encryption;

use App\MailClient\Application\Port\Out\CredentialEncryptor;
use Illuminate\Support\Facades\Crypt;

final class AesCredentialEncryptor implements CredentialEncryptor
{
    public function encrypt(string $plaintext): string
    {
        return Crypt::encryptString($plaintext);
    }

    public function decrypt(string $ciphertext): string
    {
        return Crypt::decryptString($ciphertext);
    }
}
