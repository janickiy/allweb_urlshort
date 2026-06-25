<?php

namespace App\Rules;

use App\Models\Link;
use App\Rules\Base\AbstractLimitGateRule;

class LinkLimitGateRule extends AbstractLimitGateRule
{
    /**
     * Return the model class checked by the rule.
     */
    protected static function modelClass(): string
    {
        return Link::class;
    }

    /**
     * Return the feature-map key checked by the rule.
     */
    protected static function featureKey(): string
    {
        return 'option_links';
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
