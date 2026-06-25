<?php

namespace App\Http\Requests\AdminController;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsContactRequest extends FormRequest
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
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_captcha' => ['nullable', 'integer', 'between:0,1'],
        ];
    }
}
