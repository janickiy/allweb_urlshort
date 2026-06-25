<?php

namespace App\Rules;

use App\Rules\Base\AbstractScalarRule;

class ValidatePaymentRule extends AbstractScalarRule
{
    /**
     * Determine if payment settings may be saved.
     */
    public function passes(string $attribute, int|float|string|bool|null $value): bool
    {
        return true;
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('An Extended license required to enable the payment system.');
    }
}
