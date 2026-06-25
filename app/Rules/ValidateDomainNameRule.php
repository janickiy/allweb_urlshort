<?php

namespace App\Rules;

use App\Models\Domain;
use App\Rules\Base\AbstractStringRule;

class ValidateDomainNameRule extends AbstractStringRule
{
    /**
     * Determine if the normalized domain host is unused.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function passes(string $attribute, string $value): bool
    {
        $host = parse_url($value, PHP_URL_HOST) ?: preg_replace('/^https?:\/\//', '', $value);

        if (!is_string($host) || $host === '') {
            return false;
        }

        return !Domain::whereIn('name', [$host, 'http://'.$host, 'https://'.$host])->exists();
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('This domain is already being used.');
    }
}
