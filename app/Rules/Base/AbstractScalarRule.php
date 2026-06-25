<?php

namespace App\Rules\Base;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

abstract class AbstractScalarRule implements ValidationRule
{
    /**
     * Validate a scalar or null value with a typed rule implementation.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ((!is_scalar($value) && $value !== null) || !$this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /**
     * Determine whether the typed scalar value is valid.
     *
     * @param string $attribute
     * @param int|float|string|bool|null $value
     * @return bool
     */
    abstract public function passes(string $attribute, int|float|string|bool|null $value): bool;

    /**
     * Return the validation error message.
     */
    abstract public function message(): string;
}
