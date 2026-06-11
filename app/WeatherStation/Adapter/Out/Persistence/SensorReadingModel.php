<?php

namespace App\WeatherStation\Adapter\Out\Persistence;

use Illuminate\Database\Eloquent\Model;

final class SensorReadingModel extends Model
{
    protected $table      = 'sensor_readings';
    public    $timestamps = false;

    protected $fillable = [
        'temperature',
        'humidity',
        'recorded_at',
    ];

    protected $casts = [
        'temperature' => 'float',
        'humidity'    => 'float',
        'recorded_at' => 'datetime',
    ];
}
