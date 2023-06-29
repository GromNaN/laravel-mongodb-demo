<?php

namespace App\Http\FormRequest;

use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required',
            'summary' => 'required',
            'content' => 'required',
            'published_at' => 'required|date',
            'tags' => 'nullable',
        ];
    }
}
