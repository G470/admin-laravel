<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ContainerSeparation
{
    /**
     * Handle an incoming request and enforce container-based route separation.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $containerType = env('CONTAINER_TYPE', 'frontend');
        $path = $request->getPathInfo();

        // Admin container should only handle admin routes
        if ($containerType === 'admin') {
            if (!$this->isAdminRoute($path)) {
                // Redirect non-admin requests to frontend container
                $frontendUrl = env('FRONTEND_APP_URL', 'http://localhost:8000');
                return redirect($frontendUrl . $path);
            }
        }

        // Frontend container should not handle admin routes
        if ($containerType === 'frontend') {
            if ($this->isAdminRoute($path)) {
                // Redirect admin requests to admin container
                $adminUrl = env('ADMIN_APP_URL', 'http://localhost:8080');
                return redirect($adminUrl . $path);
            }
        }

        return $next($request);
    }

    /**
     * Determine if the given path is an admin route.
     */
    private function isAdminRoute(string $path): bool
    {
        $adminPrefixes = [
            '/admin',
            '/api/admin',
        ];

        foreach ($adminPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }
}
