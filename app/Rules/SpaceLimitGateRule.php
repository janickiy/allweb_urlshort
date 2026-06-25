<?php

namespace App\Rules;

use App\Traits\UserFeaturesTrait;
use Illuminate\Contracts\Validation\Rule;

class SpaceLimitGateRule implements Rule
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
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes(mixed $attribute, mixed $value): bool
    {
        $user = request()->user();

        if ($user->can('create', ['App\Models\Space', $this->getFeatures($user)['option_spaces']])) {
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
        return __('You created too many spaces.');
    }
}
