<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyPaymentEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (config('settings.stripe') == 0) {
            return redirect()->route('home');
        } else {
            if (Auth::check()) {
                $user = Auth::user();

                // If the user is not a stripe customer
                if (empty($user->stripe_id)) {
                    $user->createAsStripeCustomer();
                }
            }
        }

        return $next($request);
    }
}
