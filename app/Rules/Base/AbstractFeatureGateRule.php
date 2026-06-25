<?php

namespace App\Rules\Base;

use App\Models\Link;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

abstract class AbstractFeatureGateRule implements ValidationRule
{
    /**
     * Create a feature gate rule with the current user's feature map.
     *
     * @param array $userFeatures
     */
    public function __construct(protected readonly array $userFeatures)
    {
    }

    /**
     * Validate feature access for the current user.
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
     * Determine whether the current user can access the feature.
     */
    public function passes(string $attribute): bool
    {
        $user = request()->user();

        return $user !== null && $user->can(static::ability(), [
            Link::class,
            $this->userFeatures[static::featureKey()] ?? null,
        ]);
    }

    /**
     * Return the policy ability checked by the rule.
     */
    abstract protected static function ability(): string;

    /**
     * Return the feature-map key checked by the rule.
     */
    abstract protected static function featureKey(): string;

    /**
     * Return the validation error message.
     */
    public function message(): string
    {
        return __('You don\'t have access to this feature.');
    }
}
