<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class InstalledMiddleware
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
        // Check if the app has been installed. This prevents SQL queries before setup.
        if (file_exists(base_path('.env')) && file_exists(storage_path('installed'))) {
            return $next($request);
        }

        return redirect('/install');
    }
}
