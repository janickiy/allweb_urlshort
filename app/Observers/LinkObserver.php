<?php

namespace App\Observers;

use App\Models\Link;

class LinkObserver
{
    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\Link  $link
     * @return void
     */
    public function deleting(Link $link): void
    {
        $link->stats()->delete();
    }
}
