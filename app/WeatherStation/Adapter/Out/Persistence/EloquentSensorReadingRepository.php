<?php

namespace App\WeatherStation\Adapter\Out\Persistence;

use App\WeatherStation\Application\Port\Out\SensorReadingRepository;
use App\WeatherStation\Domain\Model\ReadingSummary;
use App\WeatherStation\Domain\Model\SensorReading;
use App\WeatherStation\Domain\Model\WeatherSummaryResult;

final class EloquentSensorReadingRepository implements SensorReadingRepository
{
    public function save(SensorReading $reading): SensorReading
    {
        $model = SensorReadingModel::create([
            'temperature' => $reading->temperature,
            'humidity'    => $reading->humidity,
            'recorded_at' => $reading->recordedAt->format('Y-m-d H:i:s'),
        ]);

        return $reading->withId((int) $model->id);
    }

    public function findByDateRange(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return SensorReadingModel::whereBetween('recorded_at', [
            $from->format('Y-m-d H:i:s'),
            $to->format('Y-m-d H:i:s'),
        ])
            ->orderBy('recorded_at')
            ->limit(1000)
            ->get()
            ->map(fn(SensorReadingModel $m) => $this->toDomain($m))
            ->all();
    }

    public function findLatest(): ?SensorReading
    {
        $model = SensorReadingModel::orderByDesc('recorded_at')->first();

        return $model ? $this->toDomain($model) : null;
    }

    public function findSummary(\DateTimeImmutable $from, \DateTimeImmutable $to): WeatherSummaryResult
    {
        $stats = SensorReadingModel::whereBetween('recorded_at', [
            $from->format('Y-m-d H:i:s'),
            $to->format('Y-m-d H:i:s'),
        ])->selectRaw(
            'MIN(temperature) as temp_min, MAX(temperature) as temp_max, AVG(temperature) as temp_avg,
             MIN(humidity)    as hum_min,  MAX(humidity)    as hum_max,  AVG(humidity)    as hum_avg,
             COUNT(*)         as cnt'
        )->first();

        $latest = SensorReadingModel::whereBetween('recorded_at', [
            $from->format('Y-m-d H:i:s'),
            $to->format('Y-m-d H:i:s'),
        ])->orderByDesc('recorded_at')->first();

        $count = (int) ($stats->cnt ?? 0);

        if ($count === 0) {
            $zero = new ReadingSummary(0.0, 0.0, 0.0, 0.0);
            return new WeatherSummaryResult($zero, $zero, 0, $from, $to);
        }

        return new WeatherSummaryResult(
            temperature: new ReadingSummary(
                min: round((float) $stats->temp_min, 2),
                max: round((float) $stats->temp_max, 2),
                avg: round((float) $stats->temp_avg, 2),
                latest: round((float) $latest->temperature, 2),
            ),
            humidity: new ReadingSummary(
                min: round((float) $stats->hum_min, 2),
                max: round((float) $stats->hum_max, 2),
                avg: round((float) $stats->hum_avg, 2),
                latest: round((float) $latest->humidity, 2),
            ),
            count: $count,
            from: $from,
            to: $to,
        );
    }

    private function toDomain(SensorReadingModel $model): SensorReading
    {
        return SensorReading::create(
            temperature: (float) $model->temperature,
            humidity: (float) $model->humidity,
            recordedAt: new \DateTimeImmutable($model->recorded_at->format('Y-m-d H:i:s'), new \DateTimeZone('UTC')),
        )->withId((int) $model->id);
    }
}
