<?php

namespace App\Observers;

use App\User;

class UserObserver
{
    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function deleting(User $user)
    {
        if ($user->isForceDeleting()) {
            $user->domains()->delete();
            $user->spaces()->delete();
            $user->links()->delete();
            $user->stats()->delete();

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
