<?php

namespace App\WeatherStation\Domain\Model;

final class WeatherSummaryResult
{
    public function __construct(
        public readonly ReadingSummary $temperature,
        public readonly ReadingSummary $humidity,
        public readonly int $count,
        public readonly \DateTimeImmutable $from,
        public readonly \DateTimeImmutable $to,
    ) {}
}
