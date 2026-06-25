<?php

namespace App\Rules;

use App\Rules\Base\AbstractFeatureGateRule;

class LinkPasswordGateRule extends AbstractFeatureGateRule
{
    /**
     * Return the policy ability checked by the rule.
     */
    protected static function ability(): string
    {
        return 'password';
    }

    /**
     * Return the feature-map key checked by the rule.
     */
    protected static function featureKey(): string
    {
        return 'option_password';
    }
}
