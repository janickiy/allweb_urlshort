<?php

namespace App\Rules;

use App\Rules\Base\AbstractFeatureGateRule;

class LinkPlatformGateRule extends AbstractFeatureGateRule
{
    /**
     * Return the policy ability checked by the rule.
     */
    protected static function ability(): string
    {
        return 'platform';
    }

    /**
     * Return the feature-map key checked by the rule.
     */
    protected static function featureKey(): string
    {
        return 'option_platform';
    }
}
