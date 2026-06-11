<?php

namespace App\WeatherStation\Adapter\In\Http;

use Illuminate\Foundation\Http\FormRequest;

final class IngestReadingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'temperature' => ['required', 'numeric', 'between:-50,60'],
            'humidity'    => ['required', 'numeric', 'between:0,100'],
        ];
    }

    public function messages(): array
    {
        return [
            'temperature.required' => 'Temperature is required.',
            'temperature.numeric'  => 'Temperature must be a number.',
            'temperature.between'  => 'Temperature must be between -50 and 60°C.',
            'humidity.required'    => 'Humidity is required.',
            'humidity.numeric'     => 'Humidity must be a number.',
            'humidity.between'     => 'Humidity must be between 0 and 100%.',
        ];
    }
}
