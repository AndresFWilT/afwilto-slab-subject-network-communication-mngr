<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Application\Huffman\Ports\EncodeWithHuffmanPort;
use App\Application\Huffman\UseCases\EncodeWithHuffmanUseCase;
use App\Application\ShannonFano\Ports\EncodeWithShannonFanoPort;
use App\Application\ShannonFano\UseCases\EncodeWithShannonFanoUseCase;
use App\WeatherStation\Application\Port\In\GetLatestReadingUseCase;
use App\WeatherStation\Application\Port\In\GetReadingSummaryUseCase;
use App\WeatherStation\Application\Port\In\IngestReadingUseCase;
use App\WeatherStation\Application\Port\In\QueryReadingsByDateRangeUseCase;
use App\WeatherStation\Application\Port\Out\SensorReadingRepository;
use App\WeatherStation\Application\UseCase\IngestReadingService;
use App\WeatherStation\Application\UseCase\QueryReadingsService;
use App\WeatherStation\Adapter\Out\Persistence\EloquentSensorReadingRepository;
use App\MailClient\Application\Port\In\AuthenticateUseCase;
use App\MailClient\Application\Port\In\SendMessageUseCase;
use App\MailClient\Application\Port\In\ListMessagesUseCase;
use App\MailClient\Application\Port\In\GetMessageUseCase;
use App\MailClient\Application\Port\Out\MailSender;
use App\MailClient\Application\Port\Out\MailRetriever;
use App\MailClient\Application\Port\Out\CredentialEncryptor;
use App\MailClient\Application\UseCase\AuthenticateService;
use App\MailClient\Application\UseCase\SendMessageService;
use App\MailClient\Application\UseCase\ListMessagesService;
use App\MailClient\Application\UseCase\GetMessageService;
use App\MailClient\Adapter\Out\Smtp\SmtpMailSender;
use App\MailClient\Adapter\Out\Pop3\Pop3MailRetriever;
use App\MailClient\Adapter\Out\Encryption\AesCredentialEncryptor;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EncodeWithHuffmanPort::class, EncodeWithHuffmanUseCase::class);
        $this->app->bind(EncodeWithShannonFanoPort::class, EncodeWithShannonFanoUseCase::class);

        $this->app->bind(SensorReadingRepository::class, EloquentSensorReadingRepository::class);
        $this->app->bind(IngestReadingUseCase::class, IngestReadingService::class);
        $this->app->bind(QueryReadingsByDateRangeUseCase::class, QueryReadingsService::class);
        $this->app->bind(GetLatestReadingUseCase::class, QueryReadingsService::class);
        $this->app->bind(GetReadingSummaryUseCase::class, QueryReadingsService::class);

        $this->app->bind(MailSender::class, SmtpMailSender::class);
        $this->app->bind(MailRetriever::class, Pop3MailRetriever::class);
        $this->app->bind(CredentialEncryptor::class, AesCredentialEncryptor::class);
        $this->app->bind(AuthenticateUseCase::class, AuthenticateService::class);
        $this->app->bind(SendMessageUseCase::class, SendMessageService::class);
        $this->app->bind(ListMessagesUseCase::class, ListMessagesService::class);
        $this->app->bind(GetMessageUseCase::class, GetMessageService::class);
    }

    public function boot(): void {}
}
