<?php

namespace App\Observers;

use App\Models\Workspace;

class WorkspaceObserver
{
    /**
     * Handle the User "deleted" event.
     *
     * @param  Workspace  $workspace
     * @return void
     */
    public function deleting(Workspace $workspace): void
    {
        // Delete all the related links, it needs to be called in
        // a loop, otherwise the delete() method won't trigger for the targeted model
        if (isset($workspace->links))
        {
            foreach ($workspace->links as $link) {
                $link->delete();
            }
        }
    }
}
