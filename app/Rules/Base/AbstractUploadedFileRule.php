<?php

namespace App\Rules\Base;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

abstract class AbstractUploadedFileRule implements ValidationRule
{
    /**
     * Validate an uploaded file with a typed rule implementation.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$value instanceof UploadedFile || !$this->passes($attribute, $value)) {
            $fail($this->message());
        }
    }

    /**
     * Determine whether the uploaded file is valid.
     *
     * @param string $attribute
     * @param UploadedFile $value
     * @return bool
     */
    abstract public function passes(string $attribute, UploadedFile $value): bool;

    /**
     * Return the validation error message.
     */
    abstract public function message(): string;
}
