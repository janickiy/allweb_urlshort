<?php

namespace App\Rules\Base;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

abstract class AbstractNullableIdRule implements ValidationRule
{
    /**
     * Validate a nullable identifier with a typed rule implementation.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (($value !== null && !is_int($value) && !is_string($value)) || !$this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /**
     * Determine whether the typed identifier value is valid.
     *
     * @param string $attribute
     * @param int|string|null $value
     * @return bool
     */
    abstract public function passes(string $attribute, int|string|null $value): bool;

    /**
     * Return the validation error message.
     */
    abstract public function message(): string;
}
