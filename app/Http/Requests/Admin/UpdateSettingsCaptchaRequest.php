<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsCaptchaRequest extends FormRequest
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
            'captcha_site_key' => ['nullable', 'string', 'max:2048'],
            'captcha_secret_key' => ['nullable', 'string', 'max:2048'],
            'captcha_registration' => ['required', 'integer', 'between:0,1'],
            'captcha_contact' => ['required', 'integer', 'between:0,1'],
            'captcha_shorten' => ['required', 'integer', 'between:0,1'],
        ];
    }
}
