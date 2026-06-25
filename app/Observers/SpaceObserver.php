<?php

namespace App\Observers;

use App\Models\Space;

class SpaceObserver
{
    /**
     * Handle the User "deleted" event.
     *
     * @param  Space  $space
     * @return void
     */
    public function deleting(Space $space): void
    {
        // Delete all the related links, it needs to be called in
        // a loop, otherwise the delete() method won't trigger for the targeted model
        if (isset($space->links))
        {
            foreach ($space->links as $link) {
                $link->delete();
            }
        }
    }
}
