<?php

namespace App\Http\Controllers;

use App\Services\StatsService;
use Illuminate\View\View;

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
     * @return View
     */
    public function index(int|string $id): View
    {
        return view('stats.content', $this->stats->general($id));
    }

    /**
     * Display geographic statistics for a link.
     *
     * @param $id
     * @return View
     */
    public function geographic(int|string $id): View
    {
        return view('stats.content', $this->stats->geographic($id));
    }

    /**
     * Display browser statistics for a link.
     *
     * @param $id
     * @return View
     */
    public function browsers(int|string $id): View
    {
        return view('stats.content', $this->stats->grouped($id, 'browsers', 'browser', 'browsers'));
    }

    /**
     * Display platform statistics for a link.
     *
     * @param $id
     * @return View
     */
    public function platforms(int|string $id): View
    {
        return view('stats.content', $this->stats->grouped($id, 'platforms', 'platform', 'platforms'));
    }

    /**
     * Display device statistics for a link.
     *
     * @param $id
     * @return View
     */
    public function devices(int|string $id): View
    {
        return view('stats.content', $this->stats->grouped($id, 'devices', 'device', 'devices'));
    }

    /**
     * Display referrer source statistics for a link.
     *
     * @param $id
     * @return View
     */
    public function sources(int|string $id): View
    {
        return view('stats.content', $this->stats->grouped($id, 'sources', 'referrer', 'referrers'));
    }

    /**
     * Display social referrer statistics for a link.
     *
     * @param $id
     * @return View
     */
    public function social(int|string $id): View
    {
        return view('stats.content', $this->stats->social($id));
    }

    /**
     * Display language statistics for a link.
     *
     * @param $id
     * @return View
     */
    public function languages(int|string $id): View
    {
        return view('stats.content', $this->stats->grouped($id, 'languages', 'language', 'languages'));
    }
}
