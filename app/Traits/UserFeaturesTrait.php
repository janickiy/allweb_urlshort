<?php


namespace App\Traits;

use App\Models\Plan;
use App\Models\User;

trait UserFeaturesTrait
{
    private const FEATURE_KEYS = [
        'option_api',
        'option_links',
        'option_workspaces',
        'option_domains',
        'option_stats',
        'option_geo',
        'option_platform',
        'option_expiration',
        'option_password',
        'option_disabled',
        'option_utm',
    ];

    /**
     * @param $user
     * @return array
     */
    protected function getFeatures(?User $user): array
    {
        $subscriptions = [];
        $features = array_fill_keys(self::FEATURE_KEYS, 0);

        // Get all the subscriptions the user is currently active on
        if ($user) {
            foreach ($user->subscriptions as $subscription) {
                if (($subscription->recurring() || $subscription->onTrial() || $subscription->onGracePeriod()) && !$subscription->hasIncompletePayment()) {
                    $subscriptions[] = $subscription->name;
                }
            }
        }

        // Get the plans
        $plans = Plan::whereIn('name', $subscriptions)->orWhere([['amount_month', '=', 0], ['amount_year', '=', 0]])->get()->toArray();

        foreach ($plans as $plan) {
            foreach (self::FEATURE_KEYS as $key) {
                $value = (int) ($plan[$key] ?? 0);
                // If unlimited
                if ($value == -1) {
                    $features[$key] = $value;
                } // If the plan option has a value, and is higher than what was previously set
                elseif ($value > 0 && $features[$key] != -1 && $value > $features[$key]) {
                    $features[$key] = $value;
                }
            }
        }

        return $features;
    }
}
