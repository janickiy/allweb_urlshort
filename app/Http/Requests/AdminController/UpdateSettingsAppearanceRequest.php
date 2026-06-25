<?php

namespace App\Http\Requests\AdminController;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsAppearanceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'logo' => ['image', 'max:2000'],
            'favicon' => ['image', 'max:2000'],
            'theme' => ['integer', 'between:0,1']
        ];
    }
}
