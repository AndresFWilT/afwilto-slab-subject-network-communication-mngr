<?php

namespace App\Adapters\In\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EncodeTextRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'text' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'text.required' => 'The text field is required.',
            'text.string'   => 'The text field must be a string.',
        ];
    }
}
