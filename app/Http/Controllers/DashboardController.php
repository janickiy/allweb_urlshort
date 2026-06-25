<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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
     * @return View
     */
    public function index(): View
    {
        return view('dashboard.content', $this->dashboard->dataFor(Auth::user()));
    }
}
