<?php

namespace App\WeatherStation\Domain\Model;

final class ReadingSummary
{
    public function __construct(
        public readonly float $min,
        public readonly float $max,
        public readonly float $avg,
        public readonly float $latest,
    ) {}
}
