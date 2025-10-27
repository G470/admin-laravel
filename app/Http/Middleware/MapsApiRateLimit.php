<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class MapsApiRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        $identifier = $user ? $user->id : $request->ip();

        // Different rate limits for different endpoints
        $endpoint = $request->route()->getName();
        $limit = $this->getRateLimit($endpoint);
        $decayMinutes = $this->getDecayMinutes($endpoint);

        $key = "maps_api:{$identifier}:{$endpoint}";

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            $retryAfter = RateLimiter::availableIn($key);

            return response()->json([
                'success' => false,
                'message' => 'Rate limit überschritten. Bitte versuchen Sie es später erneut.',
                'error' => 'rate_limit_exceeded',
                'retry_after' => $retryAfter,
                'limit' => $limit,
                'decay_minutes' => $decayMinutes
            ], 429);
        }

        RateLimiter::hit($key, $decayMinutes * 60);

        $response = $next($request);

        // Add rate limit headers to response
        $response->headers->add([
            'X-RateLimit-Limit' => $limit,
            'X-RateLimit-Remaining' => RateLimiter::remaining($key, $limit),
            'X-RateLimit-Reset' => now()->addMinutes($decayMinutes)->timestamp,
        ]);

        return $response;
    }

    /**
     * Get rate limit for specific endpoint
     */
    protected function getRateLimit(string $endpoint): int
    {
        return match ($endpoint) {
            'api.geocoding.geocode' => 60,      // 60 requests per minute
            'api.geocoding.reverse' => 60,      // 60 requests per minute
            'api.geocoding.batch' => 10,        // 10 batch requests per minute
            'api.geocoding.status' => 120,      // 120 status checks per minute
            'api.geocoding.cache.clear' => 5,   // 5 cache clears per minute
            default => 30,                      // Default: 30 requests per minute
        };
    }

    /**
     * Get decay minutes for specific endpoint
     */
    protected function getDecayMinutes(string $endpoint): int
    {
        return match ($endpoint) {
            'api.geocoding.geocode' => 1,       // 1 minute window
            'api.geocoding.reverse' => 1,       // 1 minute window
            'api.geocoding.batch' => 1,         // 1 minute window
            'api.geocoding.status' => 1,        // 1 minute window
            'api.geocoding.cache.clear' => 5,   // 5 minute window
            default => 1,                       // Default: 1 minute window
        };
    }
}