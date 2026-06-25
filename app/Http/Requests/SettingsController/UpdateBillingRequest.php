<?php

namespace App\Http\Requests\SettingsController;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBillingRequest extends FormRequest
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
            'name' => ['required'],
            'address' => ['required'],
            'city' => ['required'],
            'postal_code' => ['required'],
            'country' => ['required']
        ];
    }
}
