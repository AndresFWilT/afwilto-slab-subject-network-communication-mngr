<?php

namespace App\WeatherStation\Application\Port\Out;

use App\WeatherStation\Domain\Model\SensorReading;
use App\WeatherStation\Domain\Model\WeatherSummaryResult;

interface SensorReadingRepository
{
    public function save(SensorReading $reading): SensorReading;

    /** @return SensorReading[] */
    public function findByDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): array;

    public function findLatest(): ?SensorReading;

    public function findSummary(\DateTimeImmutable $from, \DateTimeImmutable $to): WeatherSummaryResult;
}
