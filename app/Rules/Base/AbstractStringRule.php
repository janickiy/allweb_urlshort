<?php

namespace App\Rules\Base;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

abstract class AbstractStringRule implements ValidationRule
{
    /**
     * Validate a string value with a typed rule implementation.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !$this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /**
     * Determine whether the typed string value is valid.
     *
     * @param string $attribute
     * @param string $value
     * @return bool
     */
    abstract public function passes(string $attribute, string $value): bool;

    /**
     * Return the validation error message.
     */
    abstract public function message(): string;
}
