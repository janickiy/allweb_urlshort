<?php

namespace App\Rules;

use App\Models\Domain;
use App\Rules\Base\AbstractLimitGateRule;

class DomainLimitGateRule extends AbstractLimitGateRule
{
    /**
     * Return the model class checked by the rule.
     */
    protected static function modelClass(): string
    {
        return Domain::class;
    }

    /**
     * Return the feature-map key checked by the rule.
     */
    protected static function featureKey(): string
    {
        return 'option_domains';
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('You added too many domains.');
    }
}
