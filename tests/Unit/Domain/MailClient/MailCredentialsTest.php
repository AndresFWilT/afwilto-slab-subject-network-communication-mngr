<?php

use App\MailClient\Domain\Model\MailCredentials;

test('email combines username and domain', function (): void {
    $creds = new MailCredentials('usuario01', 'enc', 'smtp.host', 25, 'pop3.host', 110, 'redes3.udistrital.edu.co', false);

    expect($creds->email())->toBe('usuario01@redes3.udistrital.edu.co');
});

test('credentials store all connection properties', function (): void {
    $creds = new MailCredentials('user', 'encrypted', 'smtp.host', 587, 'pop3.host', 995, 'example.com', true);

    expect($creds->username)->toBe('user')
        ->and($creds->smtpPort)->toBe(587)
        ->and($creds->pop3Port)->toBe(995)
        ->and($creds->tls)->toBeTrue();
});
