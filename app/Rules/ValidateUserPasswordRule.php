<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ValidateUserPasswordRule extends AbstractStringRule
{
    /**
     * Create a user password validation rule.
     */
    public function __construct(private readonly Request $request)
    {
    }

    /**
     * Determine if the provided password matches the current user's password.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function passes(string $attribute, string $value): bool
    {
        $password = $this->request->user()?->password;

        return is_string($password) && Hash::check($this->request->input($attribute), $password);
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('The current password is not correct.');
    }
}
