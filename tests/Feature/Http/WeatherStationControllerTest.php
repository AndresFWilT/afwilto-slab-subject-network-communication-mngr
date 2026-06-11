<?php

use App\WeatherStation\Application\Port\In\GetLatestReadingUseCase;
use App\WeatherStation\Application\Port\In\GetReadingSummaryUseCase;
use App\WeatherStation\Application\Port\In\IngestReadingUseCase;
use App\WeatherStation\Application\Port\In\QueryReadingsByDateRangeUseCase;
use App\WeatherStation\Domain\Model\ReadingSummary;
use App\WeatherStation\Domain\Model\SensorReading;
use App\WeatherStation\Domain\Model\WeatherSummaryResult;

beforeEach(function (): void {
    $this->reading = SensorReading::create(21.5, 65.0, new \DateTimeImmutable('2026-06-09T10:00:00Z'))
        ->withId(1);
});

// ── POST /api/v1/weather-station/readings ──────────────────────────────────

test('POST /readings returns 201 with created reading', function (): void {
    $this->app->bind(IngestReadingUseCase::class, fn() => new class($this->reading) implements IngestReadingUseCase {
        public function __construct(private SensorReading $r) {}
        public function execute(float $t, float $h): SensorReading { return $this->r; }
    });

    $response = $this->postJson('/api/v1/weather-station/readings', [
        'temperature' => 21.5,
        'humidity'    => 65.0,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.temperature', 21.5)
        ->assertJsonPath('data.humidity', 65.0)
        ->assertJsonPath('data.id', 1);
});

test('POST /readings returns 422 for out-of-range temperature', function (): void {
    $this->postJson('/api/v1/weather-station/readings', [
        'temperature' => 100.0,
        'humidity'    => 65.0,
    ])->assertStatus(422);
});

test('POST /readings returns 422 for out-of-range humidity', function (): void {
    $this->postJson('/api/v1/weather-station/readings', [
        'temperature' => 21.5,
        'humidity'    => 110.0,
    ])->assertStatus(422);
});

test('POST /readings returns 422 when fields are missing', function (): void {
    $this->postJson('/api/v1/weather-station/readings', [])->assertStatus(422);
});

// ── GET /api/v1/weather-station/readings/latest ───────────────────────────

test('GET /readings/latest returns the most recent reading', function (): void {
    $this->app->bind(GetLatestReadingUseCase::class, fn() => new class($this->reading) implements GetLatestReadingUseCase {
        public function __construct(private SensorReading $r) {}
        public function getLatest(): ?SensorReading { return $this->r; }
    });

    $this->getJson('/api/v1/weather-station/readings/latest')
        ->assertOk()
        ->assertJsonPath('data.temperature', 21.5);
});

test('GET /readings/latest returns null when no readings exist', function (): void {
    $this->app->bind(GetLatestReadingUseCase::class, fn() => new class implements GetLatestReadingUseCase {
        public function getLatest(): ?SensorReading { return null; }
    });

    $this->getJson('/api/v1/weather-station/readings/latest')
        ->assertOk()
        ->assertJsonPath('data', null);
});

// ── GET /api/v1/weather-station/readings ──────────────────────────────────

test('GET /readings returns readings array for valid date range', function (): void {
    $this->app->bind(QueryReadingsByDateRangeUseCase::class, fn() => new class($this->reading) implements QueryReadingsByDateRangeUseCase {
        public function __construct(private SensorReading $r) {}
        public function queryByDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): array {
            return ['readings' => [$this->r], 'count' => 1];
        }
    });

    $this->getJson('/api/v1/weather-station/readings?from=2026-06-09T00:00:00Z&to=2026-06-09T23:59:59Z')
        ->assertOk()
        ->assertJsonPath('data.count', 1)
        ->assertJsonCount(1, 'data.readings');
});

test('GET /readings returns 422 when date range is missing', function (): void {
    $this->getJson('/api/v1/weather-station/readings')->assertStatus(422);
});

// ── GET /api/v1/weather-station/readings/summary ──────────────────────────

test('GET /readings/summary returns aggregated statistics', function (): void {
    $summary = new WeatherSummaryResult(
        temperature: new ReadingSummary(19.0, 22.6, 21.2, 21.5),
        humidity: new ReadingSummary(60.0, 75.0, 64.1, 65.0),
        count: 72,
        from: new \DateTimeImmutable('2026-06-09T00:00:00Z'),
        to: new \DateTimeImmutable('2026-06-09T23:59:59Z'),
    );

    $this->app->bind(GetReadingSummaryUseCase::class, fn() => new class($summary) implements GetReadingSummaryUseCase {
        public function __construct(private WeatherSummaryResult $s) {}
        public function getSummary(\DateTimeImmutable $from, \DateTimeImmutable $to): WeatherSummaryResult { return $this->s; }
    });

    $this->getJson('/api/v1/weather-station/readings/summary?from=2026-06-09T00:00:00Z&to=2026-06-09T23:59:59Z')
        ->assertOk()
        ->assertJsonPath('data.count', 72)
        ->assertJsonPath('data.temperature.min', 19.0)
        ->assertJsonPath('data.humidity.max', 75.0);
});
