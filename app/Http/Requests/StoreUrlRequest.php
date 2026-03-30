<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUrlRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'url' => ['required', 'url', 'max:2048'],
            'custom_code' => ['nullable', 'string', 'alpha_dash', 'min:3', 'max:10', 'unique:urls,short_code'],
            'expires_at' => ['nullable', 'date', 'after:now'],
        ];
    }
}
