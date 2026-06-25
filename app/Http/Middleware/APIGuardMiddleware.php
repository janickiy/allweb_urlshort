<?php

namespace App\Http\Middleware;

use App\Models\Link;
use App\Traits\UserFeaturesTrait;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class APIGuardMiddleware
{
    use UserFeaturesTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        $userFeatures = $this->getFeatures($user);

        if ($user->cannot('api', [Link::class, $userFeatures['option_api']])) {
            return response()->json([
                'message' => __('You don\'t have access to this feature.'),
                'status' => 403
            ], 403);
        }

        return $next($request);
    }
}
