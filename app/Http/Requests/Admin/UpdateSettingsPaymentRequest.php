<?php

namespace App\Http\Requests\Admin;

use App\Rules\ValidatePaymentRule;
use App\Rules\ValidateStripeCredentialsRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsPaymentRequest extends FormRequest
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
            'stripe' => ['required', 'integer', 'between:0,1', new ValidatePaymentRule()],
            'stripe_key' => ['required'],
            'stripe_secret' => ['required', new ValidateStripeCredentialsRule()],
            'stripe_wh_secret' => ['required']
        ];
    }
}
