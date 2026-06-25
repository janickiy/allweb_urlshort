<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\LinkRepository;
use App\Repositories\PlanRepository;
use App\Repositories\StatRepository;

class DashboardService
{
    public function __construct(
        private readonly LinkRepository $links,
        private readonly StatRepository $stats,
        private readonly PlanRepository $plans,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function dataFor(User $user): array
    {
        return [
            'user' => $user,
            'plan' => $this->plans->free(),
            'links' => $this->links->latestForUser($user->id, 10),
            'clicks' => $this->stats->latestForUser($user->id, 10),
            'subscriptions' => $this->activeSubscriptions($user),
        ];
    }

    /**
     * @return array<int, mixed>
     */
    private function activeSubscriptions(User $user): array
    {
        $subscriptions = [];

        foreach ($user->subscriptions as $subscription) {
            if (($subscription->recurring() || $subscription->onTrial() || $subscription->onGracePeriod()) && !$subscription->hasIncompletePayment()) {
                $subscriptions[] = $subscription;
            }
        }

        return $subscriptions;
    }
}
