#!/usr/bin/env php
<?php
/**
 * Simple Database Connection Test Script
 * 
 * Usage:
 *   php scripts/test-db-connection.php
 *   OR
 *   docker exec <container-name> php /var/www/html/scripts/test-db-connection.php
 */

// Bootstrap Laravel to get environment variables
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🔍 Testing Database Connection...\n";
echo str_repeat("=", 50) . "\n\n";

// Get database configuration from Laravel config
$host = config('database.connections.' . config('database.default') . '.host', '127.0.0.1');
$port = config('database.connections.' . config('database.default') . '.port', '3306');
$database = config('database.connections.' . config('database.default') . '.database', 'laravel');
$username = config('database.connections.' . config('database.default') . '.username', 'root');
$password = config('database.connections.' . config('database.default') . '.password', '');
$driver = config('database.default', 'mysql');

echo "Configuration:\n";
echo "  Driver:   {$driver}\n";
echo "  Host:     {$host}\n";
echo "  Port:     {$port}\n";
echo "  Database: {$database}\n";
echo "  Username: {$username}\n";
echo "  Password: " . (empty($password) ? '(empty)' : str_repeat('*', strlen($password))) . "\n\n";

// Test connection
try {
    echo "Attempting connection...\n";
    
    $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ];
    
    $startTime = microtime(true);
    $pdo = new PDO($dsn, $username, $password, $options);
    $connectionTime = round((microtime(true) - $startTime) * 1000, 2);
    
    echo "✅ Connection successful! (took {$connectionTime}ms)\n\n";
    
    // Test query
    echo "Testing query execution...\n";
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as current_db, USER() as current_user");
    $result = $stmt->fetch();
    
    echo "✅ Query executed successfully!\n\n";
    echo "Database Information:\n";
    echo "  MySQL Version:  {$result['version']}\n";
    echo "  Current DB:     {$result['current_db']}\n";
    echo "  Current User:   {$result['current_user']}\n\n";
    
    // Test table access
    echo "Testing table access...\n";
    try {
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "✅ Found " . count($tables) . " table(s) in database\n";
        if (count($tables) > 0) {
            echo "  Sample tables: " . implode(', ', array_slice($tables, 0, 5));
            if (count($tables) > 5) {
                echo " ...";
            }
            echo "\n";
        }
    } catch (PDOException $e) {
        echo "⚠️  Could not list tables: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "✅ All tests passed! Database connection is working.\n";
    exit(0);
    
} catch (PDOException $e) {
    echo "\n❌ Connection failed!\n\n";
    echo "Error Details:\n";
    echo "  Code:    {$e->getCode()}\n";
    echo "  Message: {$e->getMessage()}\n\n";
    
    // Provide helpful suggestions
    echo "Troubleshooting:\n";
    if (strpos($e->getMessage(), 'Access denied') !== false) {
        echo "  - Check username and password\n";
        echo "  - Verify user has permissions to access the database\n";
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "  - Database '{$database}' does not exist\n";
        echo "  - Create the database or check DB_DATABASE environment variable\n";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false || strpos($e->getMessage(), 'No route to host') !== false) {
        echo "  - Cannot reach database server at {$host}:{$port}\n";
        echo "  - Check if database container is running\n";
        echo "  - Verify DB_HOST and DB_PORT are correct\n";
        echo "  - Ensure containers are in the same Docker network\n";
    } elseif (strpos($e->getMessage(), 'timeout') !== false) {
        echo "  - Connection timeout\n";
        echo "  - Check if database server is accessible\n";
        echo "  - Verify network connectivity\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Unexpected error: {$e->getMessage()}\n";
    exit(1);
}

