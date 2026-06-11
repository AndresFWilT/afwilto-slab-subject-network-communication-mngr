<?php

namespace App\WeatherStation\Domain\Service;

use App\WeatherStation\Domain\Model\ReadingSummary;
use App\WeatherStation\Domain\Model\SensorReading;
use App\WeatherStation\Domain\Model\WeatherSummaryResult;
use App\WeatherStation\Domain\Error\WeatherStationException;

final class ReadingAggregator
{
    /**
     * @param SensorReading[]          $readings
     * @param \DateTimeImmutable       $from
     * @param \DateTimeImmutable       $to
     */
    public function aggregate(array $readings, \DateTimeImmutable $from, \DateTimeImmutable $to): WeatherSummaryResult
    {
        if (empty($readings)) {
            throw WeatherStationException::noReadings();
        }

        $temperatures = array_map(fn(SensorReading $r) => $r->temperature, $readings);
        $humidities   = array_map(fn(SensorReading $r) => $r->humidity, $readings);

        usort($readings, fn(SensorReading $a, SensorReading $b) => $a->recordedAt <=> $b->recordedAt);
        $latest = end($readings);

        return new WeatherSummaryResult(
            temperature: new ReadingSummary(
                min: min($temperatures),
                max: max($temperatures),
                avg: round(array_sum($temperatures) / count($temperatures), 2),
                latest: $latest->temperature,
            ),
            humidity: new ReadingSummary(
                min: min($humidities),
                max: max($humidities),
                avg: round(array_sum($humidities) / count($humidities), 2),
                latest: $latest->humidity,
            ),
            count: count($readings),
            from: $from,
            to: $to,
        );
    }
}
