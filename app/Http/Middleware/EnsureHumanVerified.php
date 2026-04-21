<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureHumanVerified
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // If the user is logged in but has NOT passed the CAPTCHA yet,
        // redirect them to the human verification page.
        if ($request->user() && !session('human_verified')) {
            return redirect()->route('verification.human');
        }

        return $next($request);
    }
}