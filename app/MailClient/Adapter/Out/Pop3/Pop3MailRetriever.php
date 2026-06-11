<?php

namespace App\MailClient\Adapter\Out\Pop3;

use App\MailClient\Application\Port\Out\MailRetriever;
use App\MailClient\Domain\Error\MailClientException;
use App\MailClient\Domain\Model\EmailMessage;
use App\MailClient\Domain\Model\Mailbox;

final class Pop3MailRetriever implements MailRetriever
{
    private const TIMEOUT = 10;

    public function authenticate(string $username, string $password, string $host, int $port, bool $tls): Mailbox
    {
        $conn = $this->connect($host, $port);

        try {
            $this->read($conn);
            $this->command($conn, "USER {$username}");
            $this->command($conn, "PASS {$password}");
            $stat = $this->command($conn, 'STAT');
            $parts = explode(' ', trim(ltrim($stat, '+OK ')));
            $count = (int) ($parts[0] ?? 0);
            $this->command($conn, 'QUIT');
        } catch (MailClientException $e) {
            fclose($conn);
            throw MailClientException::authFailed($e->getMessage());
        } finally {
            if (is_resource($conn)) fclose($conn);
        }

        return new Mailbox($username, $count);
    }

    public function listMessages(string $username, string $password, string $host, int $port, bool $tls): array
    {
        $conn = $this->connect($host, $port);
        $messages = [];

        try {
            $this->read($conn);
            $this->command($conn, "USER {$username}");
            $this->command($conn, "PASS {$password}");
            $stat = $this->command($conn, 'STAT');
            $parts = explode(' ', trim(ltrim($stat, '+OK ')));
            $count = (int) ($parts[0] ?? 0);

            for ($i = 1; $i <= $count; $i++) {
                $raw = $this->retrieveMessage($conn, $i);
                $messages[] = $this->parseMessage($i, $raw);
            }

            $this->command($conn, 'QUIT');
        } catch (\Throwable $e) {
            if ($e instanceof MailClientException) throw $e;
            throw MailClientException::pop3Error($e->getMessage(), $e);
        } finally {
            if (is_resource($conn)) fclose($conn);
        }

        return $messages;
    }

    public function getMessage(string $username, string $password, string $host, int $port, bool $tls, int $id): EmailMessage
    {
        $conn = $this->connect($host, $port);

        try {
            $this->read($conn);
            $this->command($conn, "USER {$username}");
            $this->command($conn, "PASS {$password}");
            $raw = $this->retrieveMessage($conn, $id);
            $this->command($conn, 'QUIT');
        } catch (\Throwable $e) {
            if ($e instanceof MailClientException) throw $e;
            throw MailClientException::pop3Error($e->getMessage(), $e);
        } finally {
            if (is_resource($conn)) fclose($conn);
        }

        return $this->parseMessage($id, $raw);
    }

    /** @return resource */
    private function connect(string $host, int $port)
    {
        $conn = @fsockopen($host, $port, $errno, $errstr, self::TIMEOUT);
        if (!$conn) {
            throw MailClientException::pop3Error("Cannot connect to {$host}:{$port} — {$errstr}");
        }
        stream_set_timeout($conn, self::TIMEOUT);
        return $conn;
    }

    /** @param resource $conn */
    private function read($conn): string
    {
        $line = fgets($conn, 1024);
        if ($line === false) throw MailClientException::pop3Error('Connection lost');
        return rtrim($line);
    }

    /** @param resource $conn */
    private function command($conn, string $cmd): string
    {
        fwrite($conn, $cmd . "\r\n");
        $response = $this->read($conn);
        if (!str_starts_with($response, '+OK')) {
            throw MailClientException::pop3Error("Server rejected: {$response}");
        }
        return $response;
    }

    /** @param resource $conn */
    private function retrieveMessage($conn, int $id): string
    {
        fwrite($conn, "RETR {$id}\r\n");
        $firstLine = $this->read($conn);
        if (!str_starts_with($firstLine, '+OK')) {
            throw MailClientException::pop3Error("Cannot retrieve message {$id}: {$firstLine}");
        }

        $lines = [];
        while (true) {
            $line = fgets($conn, 4096);
            if ($line === false) break;
            $line = rtrim($line, "\r\n");
            if ($line === '.') break;
            $lines[] = ltrim($line, '.');
        }

        return implode("\n", $lines);
    }

    private function parseMessage(int $id, string $raw): EmailMessage
    {
        $lines  = explode("\n", $raw);
        $from   = '';
        $to     = '';
        $subject = '';
        $date   = '';
        $body   = '';
        $inBody = false;

        foreach ($lines as $line) {
            if (!$inBody && $line === '') {
                $inBody = true;
                continue;
            }
            if ($inBody) {
                $body .= $line . "\n";
                continue;
            }
            if (str_starts_with($line, 'From:'))    $from    = trim(substr($line, 5));
            if (str_starts_with($line, 'To:'))      $to      = trim(substr($line, 3));
            if (str_starts_with($line, 'Subject:')) $subject = trim(substr($line, 8));
            if (str_starts_with($line, 'Date:'))    $date    = trim(substr($line, 5));
        }

        return EmailMessage::create($id, $from, $to, $subject, $date, trim($body));
    }
}
