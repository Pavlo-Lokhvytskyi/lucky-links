<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'min:2', 'max:255'],
            'phone_number' => ['required', 'string', 'regex:/^\+?(?=(?:[^0-9]*[0-9]){7})[0-9 ()-]{7,20}$/'],
        ];
    }
}
