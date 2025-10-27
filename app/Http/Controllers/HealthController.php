<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class HealthController extends Controller
{
    /**
     * General health check endpoint
     */
    public function check()
    {
        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'container' => env('CONTAINER_TYPE', 'unknown'),
            'checks' => [
                'database' => $this->checkDatabase(),
                'redis' => $this->checkRedis(),
                'storage' => $this->checkStorage(),
            ]
        ];

        $allHealthy = collect($health['checks'])->every(fn($check) => $check['status'] === 'healthy');
        $health['status'] = $allHealthy ? 'healthy' : 'unhealthy';

        return response()->json($health, $allHealthy ? 200 : 503);
    }

    /**
     * Admin-specific health check
     */
    public function adminCheck()
    {
        if (env('CONTAINER_TYPE') !== 'admin') {
            return response()->json(['error' => 'Not an admin container'], 404);
        }

        $health = [
            'status' => 'healthy',
            'timestamp' => now()->toISOString(),
            'container' => 'admin',
            'checks' => [
                'database' => $this->checkDatabase(),
                'redis' => $this->checkRedis(),
                'admin_routes' => $this->checkAdminRoutes(),
            ]
        ];

        $allHealthy = collect($health['checks'])->every(fn($check) => $check['status'] === 'healthy');
        $health['status'] = $allHealthy ? 'healthy' : 'unhealthy';

        return response()->json($health, $allHealthy ? 200 : 503);
    }

    /**
     * Check database connectivity
     */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            return [
                'status' => 'healthy',
                'message' => 'Database connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Database connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check Redis connectivity
     */
    private function checkRedis(): array
    {
        try {
            Redis::ping();
            return [
                'status' => 'healthy',
                'message' => 'Redis connection successful'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Redis connection failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check storage accessibility
     */
    private function checkStorage(): array
    {
        try {
            $testFile = storage_path('app/health-check-' . time() . '.tmp');
            file_put_contents($testFile, 'health check');
            
            if (file_exists($testFile)) {
                unlink($testFile);
                return [
                    'status' => 'healthy',
                    'message' => 'Storage is writable'
                ];
            }
            
            return [
                'status' => 'unhealthy',
                'message' => 'Storage write test failed'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Storage check failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Check admin-specific functionality
     */
    private function checkAdminRoutes(): array
    {
        try {
            // Check if admin routes are accessible
            $adminRoutes = [
                'admin.dashboard',
                'admin.users.index',
                'admin.categories.index'
            ];
            
            foreach ($adminRoutes as $route) {
                if (!route_exists($route)) {
                    return [
                        'status' => 'unhealthy',
                        'message' => "Admin route {$route} not found"
                    ];
                }
            }
            
            return [
                'status' => 'healthy',
                'message' => 'Admin routes accessible'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Admin routes check failed: ' . $e->getMessage()
            ];
        }
    }
}

// Helper function to check if route exists
if (!function_exists('route_exists')) {
    function route_exists($name) {
        return !is_null(app('router')->getRoutes()->getByName($name));
    }
}
