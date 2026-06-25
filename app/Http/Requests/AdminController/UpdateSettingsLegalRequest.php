<?php

namespace App\Http\Requests\AdminController;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsLegalRequest extends FormRequest
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
            'legal_terms_url' => ['nullable', 'url', 'max:2048'],
            'legal_privacy_url' => ['nullable', 'url', 'max:2048'],
            'legal_cookie_url' => ['nullable', 'url', 'max:2048'],
        ];
    }
}
