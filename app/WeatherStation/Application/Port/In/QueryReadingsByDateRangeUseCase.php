<?php

namespace App\WeatherStation\Application\Port\In;

use App\WeatherStation\Domain\Model\SensorReading;

interface QueryReadingsByDateRangeUseCase
{
    /** @return array{readings: SensorReading[], count: int} */
    public function queryByDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): array;
}
