<?php

namespace App\WeatherStation\Application\Port\In;

use App\WeatherStation\Domain\Model\SensorReading;

interface IngestReadingUseCase
{
    public function execute(float $temperature, float $humidity): SensorReading;
}
