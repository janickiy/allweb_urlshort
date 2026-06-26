<?php

namespace App\Http\Requests\Install;

use Illuminate\Foundation\Http\FormRequest;

class AdminRequest extends FormRequest
{
    /**
     * Authorize first administrator creation during installation.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Return validation rules for the first administrator account.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'password_confirmation' => ['required', 'string', 'min:6', 'same:password'],
        ];
    }
}
