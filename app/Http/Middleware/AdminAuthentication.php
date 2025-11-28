<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminAuthentication
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated

        // Check if user has admin role
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to admin area.');
        }


        return $next($request);
    }
}
