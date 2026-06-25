<?php

namespace App\Rules;

use App\Rules\Base\AbstractFeatureGateRule;

class LinkPublicGateRule extends AbstractFeatureGateRule
{
    /**
     * Return the policy ability checked by the rule.
     */
    protected static function ability(): string
    {
        return 'stats';
    }

    /**
     * Return the feature-map key checked by the rule.
     */
    protected static function featureKey(): string
    {
        return 'option_stats';
    }
}
