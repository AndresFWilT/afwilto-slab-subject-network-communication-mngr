<?php

use App\MailClient\Application\Port\In\AuthenticateUseCase;
use App\MailClient\Application\Port\In\ListMessagesUseCase;
use App\MailClient\Application\Port\In\SendMessageUseCase;
use App\MailClient\Domain\Error\MailClientException;
use App\MailClient\Domain\Model\EmailMessage;
use App\MailClient\Domain\Model\MailCredentials;

function makeCredentials(): MailCredentials
{
    return new MailCredentials('testuser', 'encrypted', 'smtp.host', 25, 'pop3.host', 110, 'example.com', false);
}

// ── POST /api/v1/mail/authenticate ──────────────────────────────────────────

test('POST /mail/authenticate returns 422 for missing fields', function (): void {
    $this->postJson('/api/v1/mail/authenticate', [])->assertStatus(422);
});

test('POST /mail/authenticate returns 401 when auth fails', function (): void {
    $this->app->bind(AuthenticateUseCase::class, fn() => new class implements AuthenticateUseCase {
        public function execute(string $u, string $p, string $sh, int $sp, string $ph, int $pp, bool $tls): MailCredentials {
            throw MailClientException::authFailed('Bad credentials');
        }
    });

    $this->postJson('/api/v1/mail/authenticate', [
        'username' => 'user', 'password' => 'wrong', 'server' => 'localhost',
    ])->assertStatus(401);
});

test('POST /mail/authenticate returns token on success', function (): void {
    $creds = makeCredentials();
    $this->app->bind(AuthenticateUseCase::class, fn() => new class($creds) implements AuthenticateUseCase {
        public function __construct(private MailCredentials $c) {}
        public function execute(string $u, string $p, string $sh, int $sp, string $ph, int $pp, bool $tls): MailCredentials {
            return $this->c;
        }
    });

    $this->postJson('/api/v1/mail/authenticate', [
        'username' => 'testuser', 'password' => 'pass', 'server' => 'localhost',
    ])->assertOk()
      ->assertJsonStructure(['data' => ['token', 'username', 'email', 'expiresAt']]);
});

// ── GET /api/v1/mail/messages ───────────────────────────────────────────────

test('GET /mail/messages returns 401 without token', function (): void {
    $this->getJson('/api/v1/mail/messages')->assertStatus(401);
});

test('GET /mail/messages returns message list with valid token', function (): void {
    $message = EmailMessage::create(1, 'sender@test.com', 'recv@test.com', 'Hi', '2026-06-10', 'Hello!');

    $this->app->bind(ListMessagesUseCase::class, fn() => new class($message) implements ListMessagesUseCase {
        public function __construct(private EmailMessage $m) {}
        public function execute(MailCredentials $c): array {
            return ['messages' => [$this->m], 'totalCount' => 1];
        }
    });

    $token = buildTestToken();

    $this->getJson('/api/v1/mail/messages', ['Authorization' => "Bearer {$token}"])
        ->assertOk()
        ->assertJsonPath('data.totalCount', 1)
        ->assertJsonCount(1, 'data.messages');
});

// ── POST /api/v1/mail/messages/send ─────────────────────────────────────────

test('POST /mail/messages/send returns 200 on success', function (): void {
    $this->app->bind(SendMessageUseCase::class, fn() => new class implements SendMessageUseCase {
        public function execute(MailCredentials $c, string $to, string $s, string $b): void {}
    });

    $token = buildTestToken();

    $this->postJson('/api/v1/mail/messages/send',
        ['to' => 'dest@test.com', 'subject' => 'Test', 'body' => 'Hello'],
        ['Authorization' => "Bearer {$token}"]
    )->assertOk()->assertJsonPath('data.sent', true);
});

function buildTestToken(): string
{
    $secret = config('app.key');
    $secret = str_starts_with($secret, 'base64:') ? base64_decode(substr($secret, 7)) : $secret;
    return \App\MailClient\Adapter\In\Http\Middleware\JwtHelper::encode([
        'sub'    => 'testuser',
        'cred'   => 'encrypted',
        'server' => ['smtpHost' => 'localhost', 'smtpPort' => 25, 'pop3Host' => 'localhost', 'pop3Port' => 110, 'domain' => 'example.com', 'tls' => false],
        'iat'    => time(),
        'exp'    => time() + 3600,
    ], $secret);
}
