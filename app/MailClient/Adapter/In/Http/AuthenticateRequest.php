<?php

namespace App\MailClient\Adapter\In\Http;

use Illuminate\Foundation\Http\FormRequest;

final class AuthenticateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'server'   => ['required', 'string'],
            'smtpPort' => ['sometimes', 'integer', 'min:1', 'max:65535'],
            'pop3Port' => ['sometimes', 'integer', 'min:1', 'max:65535'],
            'tls'      => ['sometimes', 'boolean'],
        ];
    }
}
