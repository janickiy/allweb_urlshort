<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class LinkGeoGateRule implements Rule
{
    private $userFeatures;

    /**
     * Create a new rule instance.
     *
     * @param $userFeatures
     */
    public function __construct($userFeatures)
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
    public function passes($attribute, $value)
    {
        //
        $user = request()->user();

        if ($user->can('geo', ['App\Link', $this->userFeatures['option_geo']])) {
            return true;
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('You don\'t have access to this feature.');
    }
}
