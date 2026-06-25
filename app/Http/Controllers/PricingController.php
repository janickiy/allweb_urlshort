<?php

namespace App\Http\Controllers;

use App\Repositories\PlanRepository;
use Illuminate\Support\Facades\Auth;

class PricingController extends Controller
{
    /**
     * Inject plan repository used to load pricing plans.
     */
    public function __construct(private readonly PlanRepository $plans)
    {
    }

    /**
     * Display the public pricing page with visible plans.
     */
    public function index(): mixed
    {
        $user = Auth::user();
        $plans = $this->plans->visible();

        return view('pricing.index', ['user' => $user, 'plans' => $plans]);
    }
}
