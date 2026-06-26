<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username'              => ['required', 'string', 'min:2', 'max:25', 'unique:mongodb.users,username'],
            'email'                 => ['required', 'email', 'max:80', 'unique:mongodb.users,email'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
