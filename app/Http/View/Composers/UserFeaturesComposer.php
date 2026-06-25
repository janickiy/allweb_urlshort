<?php


namespace App\Http\View\Composers;


use App\Models\Page;
use App\Traits\UserFeaturesTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UserFeaturesComposer
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
        $user = Auth::user();
        $userFeatures = $this->getFeatures($user);

        $view->with('userFeatures', $userFeatures);
    }
}