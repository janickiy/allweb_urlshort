<?php

namespace App\Services;

use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "deleted" event.
     *
     * @param  User  $user
     * @return void
     */
    public function deleting(User $user): void
    {
        if ($user->isForceDeleting()) {
            foreach ($user->subscriptions as $subscription) {
                $subscription->cancelNow();
            }

            $user->subscriptions()->delete();
        } else {
            foreach ($user->subscriptions as $subscription) {
                $subscription->cancel();
            }
        }
    }
}
