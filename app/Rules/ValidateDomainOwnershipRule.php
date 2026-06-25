<?php

namespace App\Rules;

use App\Models\Domain;
use App\Rules\Base\AbstractNullableIdRule;

class ValidateDomainOwnershipRule extends AbstractNullableIdRule
{
    /**
     * Create a domain ownership rule for a user.
     */
    public function __construct(private readonly int|string $userId)
    {
    }

    /**
     * Determine if the domain belongs to the user.
     */
    public function passes(string $attribute, int|string|null $value): bool
    {
        if (empty($value)) {
            return true;
        }

        return Domain::where([['id', '=', $value], ['user_id', '=', $this->userId]])->exists();
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('Invalid domain.');
    }
}
