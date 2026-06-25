<?php

namespace App\Rules;

use App\Models\User;
use App\Rules\Base\AbstractStringRule;

class ValidateUserByEmailRule extends AbstractStringRule
{
    /**
     * Determine if a user exists for the email.
     */
    public function passes(string $attribute, string $value): bool
    {
        return User::where('email', '=', $value)->exists();
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('No user found with this email address.');
    }
}
