<?php

use App\WeatherStation\Domain\Model\SensorReading;
use App\WeatherStation\Domain\Error\WeatherStationException;

test('creates a valid sensor reading', function (): void {
    $reading = SensorReading::create(21.5, 65.0);

    expect($reading->temperature)->toBe(21.5)
        ->and($reading->humidity)->toBe(65.0)
        ->and($reading->id)->toBeNull();
});

test('uses provided recordedAt when given', function (): void {
    $timestamp = new \DateTimeImmutable('2026-06-09T10:00:00Z');
    $reading   = SensorReading::create(20.0, 50.0, $timestamp);

    expect($reading->recordedAt->format('Y-m-d'))->toBe('2026-06-09');
});

test('withId returns new instance with id assigned', function (): void {
    $reading    = SensorReading::create(21.5, 65.0);
    $persisted  = $reading->withId(42);

    expect($persisted->id)->toBe(42)
        ->and($reading->id)->toBeNull();
});

test('throws for temperature above 60', function (): void {
    expect(fn() => SensorReading::create(61.0, 50.0))
        ->toThrow(WeatherStationException::class);
});

test('throws for temperature below -50', function (): void {
    expect(fn() => SensorReading::create(-51.0, 50.0))
        ->toThrow(WeatherStationException::class);
});

test('boundary temperature 60 is valid', function (): void {
    $reading = SensorReading::create(60.0, 50.0);
    expect($reading->temperature)->toBe(60.0);
});

test('boundary temperature -50 is valid', function (): void {
    $reading = SensorReading::create(-50.0, 50.0);
    expect($reading->temperature)->toBe(-50.0);
});

test('throws for humidity above 100', function (): void {
    expect(fn() => SensorReading::create(20.0, 101.0))
        ->toThrow(WeatherStationException::class);
});

test('throws for humidity below 0', function (): void {
    expect(fn() => SensorReading::create(20.0, -1.0))
        ->toThrow(WeatherStationException::class);
});

test('exception error codes are correct', function (): void {
    $tempEx = null;
    $humEx  = null;

    try { SensorReading::create(100.0, 50.0); } catch (WeatherStationException $e) { $tempEx = $e; }
    try { SensorReading::create(20.0, 200.0); } catch (WeatherStationException $e) { $humEx  = $e; }

    expect($tempEx?->errorCode())->toBe(WeatherStationException::INVALID_TEMPERATURE)
        ->and($humEx?->errorCode())->toBe(WeatherStationException::INVALID_HUMIDITY);
});
