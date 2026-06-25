<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsEmailRequest extends FormRequest
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
            'email_driver' => ['nullable', 'string', 'max:32'],
            'email_host' => ['nullable', 'string', 'max:255'],
            'email_port' => ['nullable', 'integer', 'between:1,65535'],
            'email_encryption' => ['nullable', 'string', 'max:32'],
            'email_address' => ['nullable', 'email', 'max:255'],
            'email_username' => ['nullable', 'string', 'max:255'],
            'email_password' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
