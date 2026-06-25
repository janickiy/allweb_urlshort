<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LinkSpaceGateRule implements Rule
{
    private $userFeatures;

    /**
     * Create a new rule instance.
     *
     * @param $userFeatures
     */
    public function __construct(mixed $userFeatures)
    {
        $this->userFeatures = $userFeatures;
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
        //
        $user = request()->user();

        if ($user->can('spaces', ['App\Models\Link', $this->userFeatures['option_spaces']])) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('You don\'t have access to this feature.');
    }
}
