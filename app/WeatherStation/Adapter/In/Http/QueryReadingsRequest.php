<?php

namespace App\WeatherStation\Adapter\In\Http;

use Illuminate\Foundation\Http\FormRequest;

final class QueryReadingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'from' => ['required', 'date'],
            'to'   => ['required', 'date', 'after:from'],
        ];
    }

    public function messages(): array
    {
        return [
            'from.required' => "The 'from' date is required.",
            'from.date'     => "The 'from' parameter must be a valid date.",
            'to.required'   => "The 'to' date is required.",
            'to.date'       => "The 'to' parameter must be a valid date.",
            'to.after'      => "The 'to' date must be after the 'from' date.",
        ];
    }

    public function fromDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->validated('from'), new \DateTimeZone('UTC'));
    }

    public function toDate(): \DateTimeImmutable
    {
        return new \DateTimeImmutable($this->validated('to'), new \DateTimeZone('UTC'));
    }
}
