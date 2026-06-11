<?php

namespace App\WeatherStation\Application\Port\In;

use App\WeatherStation\Domain\Model\WeatherSummaryResult;

interface GetReadingSummaryUseCase
{
    public function getSummary(\DateTimeImmutable $from, \DateTimeImmutable $to): WeatherSummaryResult;
}
