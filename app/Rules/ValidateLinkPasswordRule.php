<?php

namespace App\Rules;

use App\Rules\Base\AbstractStringRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ValidateLinkPasswordRule extends AbstractStringRule
{
    /**
     * Create a link password validation rule.
     */
    public function __construct(
        private readonly Request $request,
        private readonly string $password,
    ) {
    }

    /**
     * Determine if the provided password matches the link password.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    public function passes(string $attribute, string $value): bool
    {
        return Hash::check($this->request->input($attribute), $this->password);
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('The entered password is not correct.');
    }
}
