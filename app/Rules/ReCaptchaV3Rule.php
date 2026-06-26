<?php

namespace App\Rules;

use App\Services\ReCaptchaV3Service;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReCaptchaV3Rule implements ValidationRule
{
    /**
     * Create a rule for the expected reCAPTCHA v3 action.
     */
    public function __construct(private readonly string $action)
    {
    }

    /**
     * Validate that the submitted reCAPTCHA v3 token is trusted.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $token = is_string($value) ? $value : null;

        if (! app(ReCaptchaV3Service::class)->verify($token, $this->action, request()->ip())) {
            $fail(__('Captcha validation failed.'));
        }
    }
}
