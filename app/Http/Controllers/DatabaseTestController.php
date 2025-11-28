<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DatabaseTestController extends Controller
{
    /**
     * Test database connection via HTTP endpoint
     * 
     * @return JsonResponse
     */
    public function test(): JsonResponse
    {
        $result = [
            'status' => 'testing',
            'timestamp' => now()->toISOString(),
            'config' => [
                'driver' => config('database.default'),
                'host' => config('database.connections.' . config('database.default') . '.host'),
                'port' => config('database.connections.' . config('database.default') . '.port'),
                'database' => config('database.connections.' . config('database.default') . '.database'),
                'username' => config('database.connections.' . config('database.default') . '.username'),
            ],
            'connection' => null,
            'query_test' => null,
            'tables' => null,
        ];

        try {
            // Test connection
            $startTime = microtime(true);
            $pdo = DB::connection()->getPdo();
            $connectionTime = round((microtime(true) - $startTime) * 1000, 2);

            $result['connection'] = [
                'status' => 'success',
                'message' => 'Database connection successful',
                'connection_time_ms' => $connectionTime,
            ];

            // Test query
            try {
                $version = DB::selectOne("SELECT VERSION() as version, DATABASE() as current_db, USER() as current_user");
                
                $result['query_test'] = [
                    'status' => 'success',
                    'message' => 'Query executed successfully',
                    'mysql_version' => $version->version,
                    'current_database' => $version->current_db,
                    'current_user' => $version->current_user,
                ];
            } catch (\Exception $e) {
                $result['query_test'] = [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }

            // List tables
            try {
                $tables = DB::select("SHOW TABLES");
                $tableCount = count($tables);
                
                $result['tables'] = [
                    'status' => 'success',
                    'count' => $tableCount,
                    'message' => "Found {$tableCount} table(s)",
                ];
            } catch (\Exception $e) {
                $result['tables'] = [
                    'status' => 'error',
                    'message' => $e->getMessage(),
                ];
            }

            $result['status'] = 'success';
            $result['message'] = 'Database connection test completed successfully';

            return response()->json($result, 200);

        } catch (\Exception $e) {
            $result['status'] = 'error';
            $result['message'] = 'Database connection failed';
            $result['error'] = [
                'code' => $e->getCode(),
                'message' => $e->getMessage(),
                'type' => get_class($e),
            ];

            // Log error for debugging
            Log::error('Database connection test failed', [
                'error' => $e->getMessage(),
                'config' => $result['config'],
            ]);

            return response()->json($result, 503);
        }
    }
}

