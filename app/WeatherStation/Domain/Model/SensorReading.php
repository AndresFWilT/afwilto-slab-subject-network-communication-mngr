<?php

namespace App\WeatherStation\Domain\Model;

use App\WeatherStation\Domain\Error\WeatherStationException;

final class SensorReading
{
    private function __construct(
        public readonly float $temperature,
        public readonly float $humidity,
        public readonly \DateTimeImmutable $recordedAt,
        public readonly ?int $id = null,
    ) {}

    public static function create(
        float $temperature,
        float $humidity,
        ?\DateTimeImmutable $recordedAt = null,
    ): self {
        if ($temperature < -50.0 || $temperature > 60.0) {
            throw WeatherStationException::invalidTemperature($temperature);
        }

        if ($humidity < 0.0 || $humidity > 100.0) {
            throw WeatherStationException::invalidHumidity($humidity);
        }

        return new self(
            temperature: $temperature,
            humidity: $humidity,
            recordedAt: $recordedAt ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC')),
        );
    }

    public function withId(int $id): self
    {
        return new self(
            temperature: $this->temperature,
            humidity: $this->humidity,
            recordedAt: $this->recordedAt,
            id: $id,
        );
    }
}
