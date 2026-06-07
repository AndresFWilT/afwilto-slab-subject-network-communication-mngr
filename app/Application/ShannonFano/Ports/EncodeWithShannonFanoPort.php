<?php

namespace App\Application\ShannonFano\Ports;

use App\Domain\ShannonFano\ShannonFanoResult;

interface EncodeWithShannonFanoPort
{
    public function execute(string $text): ShannonFanoResult;
}
