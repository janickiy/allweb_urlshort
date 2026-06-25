<?php

namespace App\Rules\Base;

use App\Models\User;
use App\Traits\UserFeaturesTrait;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

abstract class AbstractLimitGateRule implements ValidationRule
{
    use UserFeaturesTrait;

    /**
     * Validate creation limits for the current user.
     *
     * @param string $attribute
     * @param mixed $value
     * @param Closure $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->passes($attribute)) {
            $fail($this->message());
        }
    }

    /**
     * Determine whether the current user is within the creation limit.
     */
    public function passes(string $attribute): bool
    {
        $user = request()->user();

        return $user instanceof User && $user->can('create', [
            static::modelClass(),
            $this->getFeatures($user)[static::featureKey()] ?? null,
        ]);
    }

    /**
     * Return the model class checked by the rule.
     */
    abstract protected static function modelClass(): string;

    /**
     * Return the feature-map key checked by the rule.
     */
    abstract protected static function featureKey(): string;

    /**
     * Return the validation error message.
     */
    abstract public function message(): string;
}
