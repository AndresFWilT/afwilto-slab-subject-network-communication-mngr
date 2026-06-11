<?php

use App\MailClient\Domain\Model\EmailMessage;

test('create sets bodyPreview to first 200 chars of body', function (): void {
    $body    = str_repeat('x', 300);
    $message = EmailMessage::create(1, 'from@test.com', 'to@test.com', 'Hello', '2026-06-10', $body);

    expect(mb_strlen($message->bodyPreview))->toBe(200);
});

test('create defaults subject to (no subject) when empty', function (): void {
    $message = EmailMessage::create(1, 'a@b.com', 'c@d.com', '', '2026-06-10', 'body');

    expect($message->subject)->toBe('(no subject)');
});

test('create preserves all fields', function (): void {
    $message = EmailMessage::create(42, 'alice@test.com', 'bob@test.com', 'Test', '2026-06-10', 'Hello body');

    expect($message->id)->toBe(42)
        ->and($message->from)->toBe('alice@test.com')
        ->and($message->to)->toBe('bob@test.com')
        ->and($message->subject)->toBe('Test')
        ->and($message->body)->toBe('Hello body');
});
