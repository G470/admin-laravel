<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Skip if user not authenticated
        if (!$user) {
            return $next($request);
        }

        // Skip if 2FA not enabled for user
        if (!$user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        // Skip if already verified in session
        if (session('2fa_verified')) {
            return $next($request);
        }

        // Skip for 2FA verification routes
        if ($request->routeIs('two-factor.verify') || $request->routeIs('two-factor.*')) {
            return $next($request);
        }

        // Redirect to 2FA verification
        return redirect()->route('two-factor.verify');
    }
}
