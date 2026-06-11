<?php

namespace App\MailClient\Adapter\In\Http;

use App\MailClient\Adapter\In\Http\Middleware\JwtHelper;
use App\MailClient\Application\Port\In\AuthenticateUseCase;
use App\MailClient\Application\Port\In\GetMessageUseCase;
use App\MailClient\Application\Port\In\ListMessagesUseCase;
use App\MailClient\Application\Port\In\SendMessageUseCase;
use App\MailClient\Domain\Model\EmailMessage;
use App\MailClient\Domain\Model\MailCredentials;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

final class MailClientController extends Controller
{
    private AuthenticateUseCase $authenticate;
    private SendMessageUseCase  $send;
    private ListMessagesUseCase $list;
    private GetMessageUseCase   $get;

    public function __construct(
        AuthenticateUseCase $authenticate,
        SendMessageUseCase  $send,
        ListMessagesUseCase $list,
        GetMessageUseCase   $get,
    ) {
        $this->authenticate = $authenticate;
        $this->send         = $send;
        $this->list         = $list;
        $this->get          = $get;
    }

    public function authenticate(AuthenticateRequest $request): JsonResponse
    {
        $credentials = $this->authenticate->execute(
            username: $request->validated('username'),
            password: $request->validated('password'),
            smtpHost: $request->validated('server'),
            smtpPort: (int) $request->input('smtpPort', 25),
            pop3Host: $request->validated('server'),
            pop3Port: (int) $request->input('pop3Port', 110),
            tls:      (bool) $request->input('tls', false),
        );

        $ttl    = (int) config('mail-client.jwt_ttl', 3600);
        $expAt  = time() + $ttl;
        $secret = $this->jwtSecret();

        $token = JwtHelper::encode([
            'sub'    => $credentials->username,
            'cred'   => $credentials->encryptedPassword,
            'server' => [
                'smtpHost' => $credentials->smtpHost,
                'smtpPort' => $credentials->smtpPort,
                'pop3Host' => $credentials->pop3Host,
                'pop3Port' => $credentials->pop3Port,
                'domain'   => $credentials->domain,
                'tls'      => $credentials->tls,
            ],
            'iat' => time(),
            'exp' => $expAt,
        ], $secret);

        return response()->json(['data' => [
            'token'    => $token,
            'username' => $credentials->username,
            'email'    => $credentials->email(),
            'expiresAt' => date(\DateTimeInterface::ATOM, $expAt),
        ]]);
    }

    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        $credentials = $this->mailCredentials($request);
        $this->send->execute(
            credentials: $credentials,
            to:          $request->validated('to'),
            subject:     $request->validated('subject'),
            body:        $request->validated('body'),
        );
        return response()->json(['data' => ['sent' => true]]);
    }

    public function listMessages(Request $request): JsonResponse
    {
        $result = $this->list->execute($this->mailCredentials($request));
        return response()->json(['data' => [
            'messages'   => array_map(fn(EmailMessage $m) => $this->messageToArray($m), $result['messages']),
            'totalCount' => $result['totalCount'],
        ]]);
    }

    public function getMessage(Request $request, int $id): JsonResponse
    {
        $message = $this->get->execute($this->mailCredentials($request), $id);
        return response()->json(['data' => $this->messageToArray($message)]);
    }

    private function mailCredentials(Request $request): MailCredentials
    {
        return $request->attributes->get('mailCredentials');
    }

    private function messageToArray(EmailMessage $m): array
    {
        return [
            'id'          => $m->id,
            'from'        => $m->from,
            'to'          => $m->to,
            'subject'     => $m->subject,
            'date'        => $m->date,
            'body'        => $m->body,
            'bodyPreview' => $m->bodyPreview,
        ];
    }

    private function jwtSecret(): string
    {
        $key = config('app.key', '');
        return str_starts_with($key, 'base64:')
            ? base64_decode(substr($key, 7))
            : $key;
    }
}
