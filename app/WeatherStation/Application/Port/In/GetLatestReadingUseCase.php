<?php

namespace App\WeatherStation\Application\Port\In;

use App\WeatherStation\Domain\Model\SensorReading;

interface GetLatestReadingUseCase
{
    public function getLatest(): ?SensorReading;
}
