<?php

namespace App\WeatherStation\Application\UseCase;

use App\WeatherStation\Application\Port\In\GetLatestReadingUseCase;
use App\WeatherStation\Application\Port\In\GetReadingSummaryUseCase;
use App\WeatherStation\Application\Port\In\QueryReadingsByDateRangeUseCase;
use App\WeatherStation\Application\Port\Out\SensorReadingRepository;
use App\WeatherStation\Domain\Model\SensorReading;
use App\WeatherStation\Domain\Model\WeatherSummaryResult;
use App\WeatherStation\Domain\Error\WeatherStationException;

final class QueryReadingsService implements
    QueryReadingsByDateRangeUseCase,
    GetLatestReadingUseCase,
    GetReadingSummaryUseCase
{
    private SensorReadingRepository $repository;

    public function __construct(SensorReadingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function queryByDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        if ($from > $to) {
            throw WeatherStationException::invalidDateRange();
        }

        $readings = $this->repository->findByDateRange($from, $to);

        return ['readings' => $readings, 'count' => count($readings)];
    }

    public function getLatest(): ?SensorReading
    {
        return $this->repository->findLatest();
    }

    public function getSummary(\DateTimeImmutable $from, \DateTimeImmutable $to): WeatherSummaryResult
    {
        if ($from > $to) {
            throw WeatherStationException::invalidDateRange();
        }

        return $this->repository->findSummary($from, $to);
    }
}
