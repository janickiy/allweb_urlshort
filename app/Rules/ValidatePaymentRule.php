<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidatePaymentRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }


    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('An Extended license required to enable the payment system.');
    }
}
