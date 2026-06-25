<?php

namespace App\Rules;

use App\Models\Link;
use App\Traits\UserFeaturesTrait;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class LinkLimitGateRule implements Rule
{
    use UserFeaturesTrait;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes(mixed $attribute, mixed $value): bool
    {
        $user = request()->user();

        if ($user->can('create', ['App\Models\Link', $this->getFeatures($user)['option_links']])) {
            return true;
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('You shortened too many links.');
    }
}
