<?php

namespace App\Rules;

use App\Models\Space;
use App\Rules\Base\AbstractNullableIdRule;

class ValidateSpaceOwnershipRule extends AbstractNullableIdRule
{
    /**
     * Create a space ownership rule for a user.
     */
    public function __construct(private readonly int|string $userId)
    {
    }

    /**
     * Determine if the space belongs to the user.
     */
    public function passes(string $attribute, int|string|null $value): bool
    {
        if (empty($value)) {
            return true;
        }

        return Space::where([['id', '=', $value], ['user_id', '=', $this->userId]])->exists();
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('Invalid space.');
    }
}
