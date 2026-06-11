<?php

namespace App\WeatherStation\Adapter\In\Http;

use App\WeatherStation\Application\Port\In\GetLatestReadingUseCase;
use App\WeatherStation\Application\Port\In\GetReadingSummaryUseCase;
use App\WeatherStation\Application\Port\In\IngestReadingUseCase;
use App\WeatherStation\Application\Port\In\QueryReadingsByDateRangeUseCase;
use App\WeatherStation\Domain\Model\SensorReading;
use App\WeatherStation\Domain\Model\WeatherSummaryResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

final class WeatherStationController extends Controller
{
    private IngestReadingUseCase $ingest;
    private QueryReadingsByDateRangeUseCase $queryByRange;
    private GetLatestReadingUseCase $getLatest;
    private GetReadingSummaryUseCase $getSummary;

    public function __construct(
        IngestReadingUseCase $ingest,
        QueryReadingsByDateRangeUseCase $queryByRange,
        GetLatestReadingUseCase $getLatest,
        GetReadingSummaryUseCase $getSummary,
    ) {
        $this->ingest       = $ingest;
        $this->queryByRange = $queryByRange;
        $this->getLatest    = $getLatest;
        $this->getSummary   = $getSummary;
    }

    public function ingest(IngestReadingRequest $request): JsonResponse
    {
        $reading = $this->ingest->execute(
            (float) $request->validated('temperature'),
            (float) $request->validated('humidity'),
        );

        return response()->json(['data' => $this->readingToArray($reading)], 201);
    }

    public function queryReadings(QueryReadingsRequest $request): JsonResponse
    {
        $result = $this->queryByRange->queryByDateRange(
            $request->fromDate(),
            $request->toDate(),
        );

        return response()->json([
            'data' => [
                'readings' => array_map(fn(SensorReading $r) => $this->readingToArray($r), $result['readings']),
                'count'    => $result['count'],
            ],
        ]);
    }

    public function latest(): JsonResponse
    {
        $reading = $this->getLatest->getLatest();

        if ($reading === null) {
            return response()->json(['data' => null]);
        }

        return response()->json(['data' => $this->readingToArray($reading)]);
    }

    public function summary(QueryReadingsRequest $request): JsonResponse
    {
        $result = $this->getSummary->getSummary(
            $request->fromDate(),
            $request->toDate(),
        );

        return response()->json(['data' => $this->summaryToArray($result)]);
    }

    private function readingToArray(SensorReading $reading): array
    {
        return [
            'id'          => $reading->id,
            'temperature' => $reading->temperature,
            'humidity'    => $reading->humidity,
            'recordedAt'  => $reading->recordedAt->format(\DateTimeInterface::ATOM),
        ];
    }

    private function summaryToArray(WeatherSummaryResult $result): array
    {
        return [
            'temperature' => [
                'min'    => $result->temperature->min,
                'max'    => $result->temperature->max,
                'avg'    => $result->temperature->avg,
                'latest' => $result->temperature->latest,
            ],
            'humidity' => [
                'min'    => $result->humidity->min,
                'max'    => $result->humidity->max,
                'avg'    => $result->humidity->avg,
                'latest' => $result->humidity->latest,
            ],
            'count' => $result->count,
            'from'  => $result->from->format(\DateTimeInterface::ATOM),
            'to'    => $result->to->format(\DateTimeInterface::ATOM),
        ];
    }
}
