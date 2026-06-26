<?php

namespace App\Rules;

use App\Models\Workspace;
use App\Rules\Base\AbstractNullableIdRule;

class ValidateWorkspaceOwnershipRule extends AbstractNullableIdRule
{
    /**
     * Create a workspace ownership rule for a user.
     */
    public function __construct(private readonly int|string $userId)
    {
    }

    /**
     * Determine if the workspace belongs to the user.
     *
     * @param string $attribute
     * @param int|string|null $value
     * @return bool
     */
    public function passes(string $attribute, int|string|null $value): bool
    {
        if (empty($value)) {
            return true;
        }

        return Workspace::where([['id', '=', $value], ['user_id', '=', $this->userId]])->exists();
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('Invalid workspace.');
    }
}
