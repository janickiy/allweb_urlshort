<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;

class ValidatePlatformKeyRule extends AbstractStringRule
{
    /**
     * Determine if the platform key exists in configuration.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function passes(string $attribute, string $value): bool
    {
        $platforms = config('platforms');

        return is_array($platforms) && in_array($value, $platforms, true);
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return 'Invalid platform';
    }
}
