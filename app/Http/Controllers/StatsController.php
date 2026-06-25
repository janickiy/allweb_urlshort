<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    /**
     * Inject statistics service used by stats screens.
     */
    public function __construct(private readonly StatsService $stats)
    {
    }

    /**
     * Display the general statistics screen for a link.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(mixed $id): mixed
    {
        return view('stats.content', $this->stats->general($id));
    }

    /**
     * Display geographic statistics for a link.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function geographic(Request $request, mixed $id): mixed
    {
        return view('stats.content', $this->stats->geographic($id));
    }

    /**
     * Display browser statistics for a link.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function browsers(Request $request, mixed $id): mixed
    {
        return view('stats.content', $this->stats->grouped($id, 'browsers', 'browser', 'browsers'));
    }

    /**
     * Display platform statistics for a link.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function platforms(Request $request, mixed $id): mixed
    {
        return view('stats.content', $this->stats->grouped($id, 'platforms', 'platform', 'platforms'));
    }

    /**
     * Display device statistics for a link.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function devices(Request $request, mixed $id): mixed
    {
        return view('stats.content', $this->stats->grouped($id, 'devices', 'device', 'devices'));
    }

    /**
     * Display referrer source statistics for a link.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function sources(Request $request, mixed $id): mixed
    {
        return view('stats.content', $this->stats->grouped($id, 'sources', 'referrer', 'referrers'));
    }

    /**
     * Display social referrer statistics for a link.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function social(Request $request, mixed $id): mixed
    {
        return view('stats.content', $this->stats->social($id));
    }

    /**
     * Display language statistics for a link.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function languages(Request $request, mixed $id): mixed
    {
        return view('stats.content', $this->stats->grouped($id, 'languages', 'language', 'languages'));
    }
}
