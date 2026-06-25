<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * @var
     */
    protected $auth;

    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If the user is a guest, or doesn't have permissions
        if ($this->auth->guest() || $this->auth->user()->role !== 1) {
            return redirect()->route('home');
        }

        return $next($request);
    }
}
