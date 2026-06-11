<?php

namespace App\WeatherStation\Domain\Error;

final class WeatherStationException extends \RuntimeException
{
    public const INVALID_TEMPERATURE = 'INVALID_TEMPERATURE';
    public const INVALID_HUMIDITY    = 'INVALID_HUMIDITY';
    public const INVALID_DATE_RANGE  = 'INVALID_DATE_RANGE';
    public const NO_READINGS         = 'NO_READINGS';

    private function __construct(
        private readonly string $errorCode,
        string $message,
    ) {
        parent::__construct($message);
    }

    public static function invalidTemperature(float $value): self
    {
        return new self(
            self::INVALID_TEMPERATURE,
            "Temperature {$value}°C is outside the valid range [-50, 60]°C.",
        );
    }

    public static function invalidHumidity(float $value): self
    {
        return new self(
            self::INVALID_HUMIDITY,
            "Humidity {$value}% is outside the valid range [0, 100]%.",
        );
    }

    public static function invalidDateRange(): self
    {
        return new self(
            self::INVALID_DATE_RANGE,
            "The 'from' date must be before the 'to' date.",
        );
    }

    public static function noReadings(): self
    {
        return new self(
            self::NO_READINGS,
            'No sensor readings found.',
        );
    }

    public function errorCode(): string
    {
        return $this->errorCode;
    }
}
