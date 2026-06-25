<?php

namespace App\Http\Requests\AdminController;

use App\Rules\ValidateUserByEmailRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateSubscriptionRequest extends FormRequest
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
            'email' => ['required', new ValidateUserByEmailRule()],
            'plan' => ['required'],
            'trial_days' => ['required', 'integer', 'min:1']
        ];
    }
}
