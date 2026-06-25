<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;

class ValidateGeoKeyRule extends AbstractStringRule
{
    /**
     * Determine if the country key exists in configuration.
     */
    public function passes(string $attribute, string $value): bool
    {
        $countries = config('countries');

        return is_array($countries) && array_key_exists($value, $countries);
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return 'Invalid country.';
    }
}
