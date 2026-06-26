<?php

namespace App\Rules;

use App\Models\Workspace;
use App\Rules\Base\AbstractLimitGateRule;

class WorkspaceLimitGateRule extends AbstractLimitGateRule
{
    /**
     * Return the model class checked by the rule.
     */
    protected static function modelClass(): string
    {
        return Workspace::class;
    }

    /**
     * Return the feature-map key checked by the rule.
     */
    protected static function featureKey(): string
    {
        return 'option_workspaces';
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('You created too many workspaces.');
    }
}
