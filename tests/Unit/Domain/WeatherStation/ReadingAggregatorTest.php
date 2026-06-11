<?php

use App\WeatherStation\Domain\Model\SensorReading;
use App\WeatherStation\Domain\Service\ReadingAggregator;
use App\WeatherStation\Domain\Error\WeatherStationException;

function makeReading(float $temp, float $humidity, string $time): SensorReading
{
    return SensorReading::create($temp, $humidity, new \DateTimeImmutable($time));
}

test('computes correct min temperature', function (): void {
    $readings  = [
        makeReading(20.0, 65.0, '2026-06-09T10:00:00Z'),
        makeReading(22.5, 60.0, '2026-06-09T10:15:00Z'),
        makeReading(19.0, 70.0, '2026-06-09T10:30:00Z'),
    ];
    $from   = new \DateTimeImmutable('2026-06-09T00:00:00Z');
    $to     = new \DateTimeImmutable('2026-06-09T23:59:59Z');
    $result = (new ReadingAggregator())->aggregate($readings, $from, $to);

    expect($result->temperature->min)->toBe(19.0);
});

test('computes correct max humidity', function (): void {
    $readings = [
        makeReading(20.0, 65.0, '2026-06-09T10:00:00Z'),
        makeReading(22.5, 75.0, '2026-06-09T10:15:00Z'),
        makeReading(19.0, 60.0, '2026-06-09T10:30:00Z'),
    ];
    $from   = new \DateTimeImmutable('2026-06-09T00:00:00Z');
    $to     = new \DateTimeImmutable('2026-06-09T23:59:59Z');
    $result = (new ReadingAggregator())->aggregate($readings, $from, $to);

    expect($result->humidity->max)->toBe(75.0);
});

test('computes correct averages', function (): void {
    $readings = [
        makeReading(20.0, 60.0, '2026-06-09T10:00:00Z'),
        makeReading(22.0, 70.0, '2026-06-09T10:15:00Z'),
        makeReading(24.0, 80.0, '2026-06-09T10:30:00Z'),
    ];
    $from   = new \DateTimeImmutable('2026-06-09T00:00:00Z');
    $to     = new \DateTimeImmutable('2026-06-09T23:59:59Z');
    $result = (new ReadingAggregator())->aggregate($readings, $from, $to);

    expect($result->temperature->avg)->toBe(22.0)
        ->and($result->humidity->avg)->toBe(70.0);
});

test('latest values come from the most recent reading', function (): void {
    $readings = [
        makeReading(20.0, 60.0, '2026-06-09T10:00:00Z'),
        makeReading(25.0, 80.0, '2026-06-09T11:00:00Z'),
        makeReading(22.0, 65.0, '2026-06-09T10:30:00Z'),
    ];
    $from   = new \DateTimeImmutable('2026-06-09T00:00:00Z');
    $to     = new \DateTimeImmutable('2026-06-09T23:59:59Z');
    $result = (new ReadingAggregator())->aggregate($readings, $from, $to);

    expect($result->temperature->latest)->toBe(25.0)
        ->and($result->humidity->latest)->toBe(80.0);
});

test('count equals number of readings', function (): void {
    $readings = [
        makeReading(20.0, 60.0, '2026-06-09T10:00:00Z'),
        makeReading(21.0, 62.0, '2026-06-09T10:15:00Z'),
        makeReading(22.0, 64.0, '2026-06-09T10:30:00Z'),
        makeReading(23.0, 66.0, '2026-06-09T10:45:00Z'),
        makeReading(24.0, 68.0, '2026-06-09T11:00:00Z'),
    ];
    $from   = new \DateTimeImmutable('2026-06-09T00:00:00Z');
    $to     = new \DateTimeImmutable('2026-06-09T23:59:59Z');
    $result = (new ReadingAggregator())->aggregate($readings, $from, $to);

    expect($result->count)->toBe(5);
});

test('throws for empty readings array', function (): void {
    expect(fn() => (new ReadingAggregator())->aggregate(
        [],
        new \DateTimeImmutable('2026-06-09T00:00:00Z'),
        new \DateTimeImmutable('2026-06-09T23:59:59Z'),
    ))->toThrow(WeatherStationException::class);
});
