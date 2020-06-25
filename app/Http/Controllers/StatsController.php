<?php

namespace App\Http\Controllers;

use App\{Link,Stat};
use App\Traits\UserFeaturesTrait;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatsController extends Controller
{
    use UserFeaturesTrait;

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index($id)
    {
        $link = Link::where('id', $id)->firstOrFail();

        $this->statsGuard($link);

        $user = User::findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        $clicks = null;
        if ($user->can('stats', ['App\Link', $remoteUserFeatures['option_stats']])) {
            $clicks = Stat::where('link_id', $link->id)
                ->orderBy('id', 'desc')
                ->paginate(10);
        }

        $stats = [
            'Last 24 hours' => [
                'current' => Stat::where('link_id', $link->id)->whereDate('created_at', '>', Carbon::now()->subDays(1))->count(),
                'previous' => Stat::where('link_id', $link->id)->whereBetween('created_at', [Carbon::now()->subDays(2), Carbon::now()->subDays(1)])->count()],
            'Last 30 days' => [
                'current' => Stat::where('link_id', $link->id)->whereDate('created_at', '>', Carbon::now()->subDays(30))->count(),
                'previous' => Stat::where('link_id', $link->id)->whereBetween('created_at', [Carbon::now()->subDays(60), Carbon::now()->subDays(30)])->count()],
            'All time' => [
                'current' => $link->clicks
            ]
        ];

        return view('stats.content', ['view' => 'general', 'link' => $link, 'clicks' => $clicks, 'stats' => $stats, 'user' => $user, 'remoteUserFeatures' => $remoteUserFeatures]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function geographic(Request $request, $id)
    {
        $link = Link::where('id', $id)->firstOrFail();

        $this->statsGuard($link);

        $user = User::findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        $countriesChart = $countries = null;
        if ($user->can('stats', ['App\Link', $remoteUserFeatures['option_stats']])) {
            $countriesChart = Stat::select('country')
                ->selectRaw('COUNT(*) as `count`')
                ->where('link_id', $link->id)
                ->groupBy('country')
                ->orderByDesc('count')
                ->get();

            $countries = Stat::select('country')
                ->selectRaw('COUNT(*) as `count`')
                ->where('link_id', $link->id)
                ->groupBy('country')
                ->orderByDesc('count')
                ->paginate(10);
        }

        return view('stats.content', ['view' => 'geographic', 'link' => $link, 'countries' => $countries, 'countriesChart' => $countriesChart, 'user' => $user, 'remoteUserFeatures' => $remoteUserFeatures]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function browsers(Request $request, $id)
    {
        $link = Link::where('id', $id)->firstOrFail();

        $this->statsGuard($link);

        $user = User::findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        $browsers = null;
        if ($user->can('stats', ['App\Link', $remoteUserFeatures['option_stats']])) {
            $browsers = Stat::select('browser')
                ->selectRaw('COUNT(*) as `count`')
                ->where('link_id', $link->id)
                ->groupBy('browser')
                ->orderByDesc('count')
                ->paginate(10);
        }

        return view('stats.content', ['view' => 'browsers', 'link' => $link, 'browsers' => $browsers, 'user' => $user, 'remoteUserFeatures' => $remoteUserFeatures]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function platforms(Request $request, $id)
    {
        $link = Link::where('id', $id)->firstOrFail();

        $this->statsGuard($link);

        $user = User::findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        $platforms = null;
        if ($user->can('stats', ['App\Link', $remoteUserFeatures['option_stats']])) {
            $platforms = Stat::select('platform')
                ->selectRaw('COUNT(*) as `count`')
                ->where('link_id', $link->id)
                ->groupBy('platform')
                ->orderByDesc('count')
                ->paginate(10);
        }

        return view('stats.content', ['view' => 'platforms', 'link' => $link, 'platforms' => $platforms, 'user' => $user, 'remoteUserFeatures' => $remoteUserFeatures]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function devices(Request $request, $id)
    {
        $link = Link::where('id', $id)->firstOrFail();

        $this->statsGuard($link);

        $user = User::findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        $devices = null;
        if ($user->can('stats', ['App\Link', $remoteUserFeatures['option_stats']])) {
            $devices = Stat::select('device')
                ->selectRaw('COUNT(*) as `count`')
                ->where('link_id', $link->id)
                ->groupBy('device')
                ->orderByDesc('count')
                ->paginate(10);
        }

        return view('stats.content', ['view' => 'devices', 'link' => $link, 'devices' => $devices, 'user' => $user, 'remoteUserFeatures' => $remoteUserFeatures]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sources(Request $request, $id)
    {
        $link = Link::where('id', $id)->firstOrFail();

        $this->statsGuard($link);

        $user = User::findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        $referrers = null;
        if ($user->can('stats', ['App\Link', $remoteUserFeatures['option_stats']])) {
            $referrers = Stat::select('referrer')
                ->selectRaw('COUNT(*) as `count`')
                ->where('link_id', $link->id)
                ->groupBy('referrer')
                ->orderByDesc('count')
                ->paginate(10);
        }

        return view('stats.content', ['view' => 'sources', 'link' => $link, 'referrers' => $referrers, 'user' => $user, 'remoteUserFeatures' => $remoteUserFeatures]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function social(Request $request, $id)
    {
        $link = Link::where('id', $id)->firstOrFail();

        $this->statsGuard($link);

        $domains = [
            'l.facebook.com' => 'Facebook',
            't.co' => 'Twitter',
            'l.instagram.com' => 'Instagram',
            'out.reddit.com' => 'Reddit',
            'www.youtube.com' => 'YouTube',
            'away.vk.com' => 'VK',
            't.umblr.com' => 'Tumblr'
        ];

        $user = User::findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        $socials = $totalCount = null;
        if ($user->can('stats', ['App\Link', $remoteUserFeatures['option_stats']])) {
            $socials = Stat::select('referrer')
                ->selectRaw('COUNT(*) as `count`')
                ->where('link_id', $link->id)
                ->whereIn('referrer', array_keys($domains))
                ->groupBy('referrer')
                ->orderByDesc('count')
                ->paginate(10);

            $totalCount = null;
            foreach ($socials as $social) {
                $totalCount += $social->count;
            }
        }

        return view('stats.content', ['view' => 'social', 'link' => $link, 'totalCount' => $totalCount, 'domains' => $domains, 'socials' => $socials, 'user' => $user, 'remoteUserFeatures' => $remoteUserFeatures]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function languages(Request $request, $id)
    {
        $link = Link::where('id', $id)->firstOrFail();

        $this->statsGuard($link);

        $user = User::findOrFail($link->user_id);
        $remoteUserFeatures = $this->getFeatures($user);

        $languages = null;
        if ($user->can('stats', ['App\Link', $remoteUserFeatures['option_stats']])) {
            $languages = Stat::select('language')
                ->selectRaw('COUNT(*) as `count`')
                ->where('link_id', $link->id)
                ->groupBy('language')
                ->orderByDesc('count')
                ->paginate(10);
        }

        return view('stats.content', ['view' => 'languages', 'link' => $link, 'languages' => $languages, 'user' => $user, 'remoteUserFeatures' => $remoteUserFeatures]);
    }

    /**
     * @param $link
     */
    private function statsGuard($link)
    {
        // If the link stats is not set to public
        if(!$link->public) {
            $user = Auth::user();

            // If the user is not authenticated
            // Or if the user is not the owner of the link and not an admin
            if ($user == null || $user->id !== $link->user_id && $user->role !== 1) {
                abort(403);
            }
        }
    }
}
