<?php

namespace App\MailClient\Adapter\Out\Smtp;

use App\MailClient\Application\Port\Out\MailSender;
use App\MailClient\Domain\Error\MailClientException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

final class SmtpMailSender implements MailSender
{
    public function send(
        string $from,
        string $to,
        string $subject,
        string $body,
        string $smtpHost,
        int    $smtpPort,
        bool   $tls,
    ): void {
        try {
            $transport = new EsmtpTransport($smtpHost, $smtpPort, $tls);
            $mailer    = new Mailer($transport);

            $email = (new Email())
                ->from(Address::create($from))
                ->to(Address::create($to))
                ->subject($subject)
                ->text($body);

            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw MailClientException::smtpError($e->getMessage(), $e);
        } catch (\Throwable $e) {
            throw MailClientException::smtpError($e->getMessage(), $e);
        }
    }
}
