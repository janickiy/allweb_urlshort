<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyPaymentEnabled
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
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
