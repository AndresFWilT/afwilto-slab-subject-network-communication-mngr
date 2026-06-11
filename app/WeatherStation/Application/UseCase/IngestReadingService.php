<?php

namespace App\WeatherStation\Application\UseCase;

use App\WeatherStation\Application\Port\In\IngestReadingUseCase;
use App\WeatherStation\Application\Port\Out\SensorReadingRepository;
use App\WeatherStation\Domain\Model\SensorReading;

final class IngestReadingService implements IngestReadingUseCase
{
    private SensorReadingRepository $repository;

    public function __construct(SensorReadingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(float $temperature, float $humidity): SensorReading
    {
        $reading = SensorReading::create($temperature, $humidity);

        return $this->repository->save($reading);
    }
}
