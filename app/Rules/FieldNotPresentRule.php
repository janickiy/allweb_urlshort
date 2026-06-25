<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FieldNotPresentRule implements ValidationRule
{
    /**
     * Fail validation whenever a forbidden field is present.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $fail($this->message());
    }

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return 'The validation error message.';
    }
}
