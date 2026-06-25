<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Inject dashboard services used to build dashboard data.
     */
    public function __construct(private readonly DashboardService $dashboard)
    {
    }

    /**
     * Display the authenticated user dashboard overview.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): mixed
    {
        return view('dashboard.content', $this->dashboard->dataFor(Auth::user()));
    }
}
