<?php


namespace App\Http\View\Composers;


use App\Models\Domain;
use App\Models\Link;
use App\Models\Page;
use App\Models\Space;
use App\Traits\UserFeaturesTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UserStatsComposer
{
    use UserFeaturesTrait;

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view): void
    {
        if (Auth::check()) {
            $user = Auth::user();

            $stats = [
                'links' => Link::where('user_id', $user->id)->count(),
                'spaces' => Space::where('user_id', $user->id)->count(),
                'domains' => Domain::where('user_id', $user->id)->count()
            ];

            $view->with('stats', $stats);
        }
    }
}