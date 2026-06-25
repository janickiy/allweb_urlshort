<?php

namespace App\Http\Requests\AdminController;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRegistrationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'registration_registration' => ['required', 'integer', 'between:0,1'],
            'registration_captcha' => ['nullable', 'integer', 'between:0,1'],
            'registration_verification' => ['required', 'integer', 'between:0,1'],
        ];
    }
}
