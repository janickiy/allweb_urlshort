<?php

namespace App\Rules;

use App\Models\Space;
use App\Rules\Base\AbstractLimitGateRule;

class SpaceLimitGateRule extends AbstractLimitGateRule
{
    /**
     * Return the model class checked by the rule.
     */
    protected static function modelClass(): string
    {
        return Space::class;
    }

    /**
     * Return the feature-map key checked by the rule.
     */
    protected static function featureKey(): string
    {
        return 'option_spaces';
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
