<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'realname'  => ['nullable', 'string', 'max:40'],
            'url'       => ['nullable', 'url', 'max:100'],
            'location'  => ['nullable', 'string', 'max:30'],
            'signature' => ['nullable', 'string', 'max:512'],
            'email'     => ['nullable', 'email', 'max:80'],
            'timezone'  => ['nullable', 'numeric', 'between:-12,14'],
            'language'  => ['nullable', 'string', 'max:20'],
        ];
    }
}
