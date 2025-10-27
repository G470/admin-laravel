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
        if (!Auth::check()) {
            return redirect('/admin/login');
        }

        // Check if user has admin role
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Unauthorized access to admin area.');
        }

        // Check if we're in the admin container
        if (env('CONTAINER_TYPE') !== 'admin') {
            // Redirect to admin container
            $adminUrl = env('ADMIN_APP_URL', 'http://localhost:8080');
            return redirect($adminUrl . $request->getRequestUri());
        }

        return $next($request);
    }
}
