<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsShortenerRequest extends FormRequest
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
            'short_guest' => ['required', 'integer', 'between:0,1'],
            'short_bad_words' => ['nullable', 'string'],
        ];
    }
}
