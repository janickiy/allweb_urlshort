<?php

namespace App\Http\Controllers;

use App\Plan;
use Illuminate\Support\Facades\Auth;

class PricingController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $plans = Plan::where('visibility', 1)->get();

        return view('pricing.index', ['user' => $user, 'plans' => $plans]);
    }
}
