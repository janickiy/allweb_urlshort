<?php

namespace App\Rules;

use App\Models\Domain;
use Illuminate\Contracts\Validation\Rule;

class ValidateDomainNameRule implements Rule
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
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes(mixed $attribute, mixed $value): bool
    {
        $host = parse_url($value, PHP_URL_HOST) ?: preg_replace('/^https?:\/\//', '', $value);

        if (Domain::whereIn('name', [$host, 'http://'.$host, 'https://'.$host])->exists()) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('This domain is already being used.');
    }
}
