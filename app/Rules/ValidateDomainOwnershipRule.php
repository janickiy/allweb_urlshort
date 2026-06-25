<?php

namespace App\Rules;

use App\Models\Domain;
use Illuminate\Contracts\Validation\Rule;

class ValidateDomainOwnershipRule implements Rule
{
    /**
     * @var
     */
    private $userId;

    /**
     * Create a new rule instance.
     *
     * @param $userId
     */
    public function __construct(int|string $userId)
    {
        $this->userId = $userId;
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
        if (empty($value)) {
            return true;
        }

        if (Domain::where([['id', '=', $value], ['user_id', '=', $this->userId]])->exists()) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('Invalid domain.');
    }
}
