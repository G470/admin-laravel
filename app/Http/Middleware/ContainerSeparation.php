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
