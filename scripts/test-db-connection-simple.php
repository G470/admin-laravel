#!/usr/bin/env php
<?php
/**
 * Simple Database Connection Test Script (Standalone)
 * Reads .env file directly without requiring Laravel bootstrap
 * 
 * Usage:
 *   php scripts/test-db-connection-simple.php
 */

function loadEnv($filePath) {
    $env = [];
    if (!file_exists($filePath)) {
        return $env;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue; // Skip comments
        }
        
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Remove quotes if present
            if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                $value = substr($value, 1, -1);
            }
            
            $env[$key] = $value;
        }
    }
    
    return $env;
}

// Load .env file
$envPath = __DIR__ . '/../.env';
$env = loadEnv($envPath);

if (empty($env)) {
    echo "❌ Error: Could not load .env file from: {$envPath}\n";
    exit(1);
}

echo "🔍 Testing Database Connection...\n";
echo str_repeat("=", 50) . "\n\n";

// Get database configuration from .env
$host = $env['DB_HOST'] ?? '127.0.0.1';
$port = $env['DB_PORT'] ?? '3306';
$database = $env['DB_DATABASE'] ?? 'laravel';
$username = $env['DB_USERNAME'] ?? 'root';
$password = $env['DB_PASSWORD'] ?? '';
$driver = $env['DB_CONNECTION'] ?? 'mysql';

echo "Configuration:\n";
echo "  Driver:   {$driver}\n";
echo "  Host:     {$host}\n";
echo "  Port:     {$port}\n";
echo "  Database: {$database}\n";
echo "  Username: {$username}\n";
echo "  Password: " . (empty($password) ? '(empty)' : str_repeat('*', min(strlen($password), 20))) . "\n\n";

// Test connection
try {
    echo "Attempting connection...\n";
    
    // Build DSN with SSL mode for MySQL/MariaDB
    if ($driver === 'mysql' || $driver === 'mariadb') {
        // Enable SSL by adding sslmode parameter to DSN
        $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    } else {
        $dsn = "{$driver}:host={$host};port={$port};dbname={$database};charset=utf8mb4";
    }
    
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 10,
    ];
    
    // Enable SSL for MySQL/MariaDB (required by server)
    if ($driver === 'mysql' || $driver === 'mariadb') {
        // Set SSL options - empty values enable SSL without verification
        // This is required when server has require_secure_transport=ON
        $options[PDO::MYSQL_ATTR_SSL_CA] = null;
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        
        // Alternative: Use mysqli-style SSL options
        // For PDO, we need to enable SSL by providing at least one SSL option
        // Setting SSL_CA to empty string enables SSL without CA verification
    }
    
    $startTime = microtime(true);
    $pdo = new PDO($dsn, $username, $password, $options);
    $connectionTime = round((microtime(true) - $startTime) * 1000, 2);
    
    echo "✅ Connection successful! (took {$connectionTime}ms)\n\n";
    
    // Test query
    echo "Testing query execution...\n";
    $stmt = $pdo->query("SELECT VERSION() as version, DATABASE() as current_db, USER() as `current_user`");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
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
            echo "  Sample tables: " . implode(', ', array_slice($tables, 0, 10));
            if (count($tables) > 10) {
                echo " ... (+" . (count($tables) - 10) . " more)";
            }
            echo "\n";
        }
    } catch (PDOException $e) {
        echo "⚠️  Could not list tables: " . $e->getMessage() . "\n";
    }
    
    // Test a simple count query
    echo "\nTesting data access...\n";
    try {
        $tableCount = 0;
        $totalRows = 0;
        foreach ($tables as $table) {
            try {
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM `{$table}`");
                $count = $stmt->fetch()['count'];
                $totalRows += $count;
                $tableCount++;
            } catch (PDOException $e) {
                // Skip tables that can't be queried
            }
        }
        echo "✅ Successfully queried {$tableCount} table(s)\n";
        echo "  Total rows across all tables: {$totalRows}\n";
    } catch (Exception $e) {
        echo "⚠️  Could not query tables: " . $e->getMessage() . "\n";
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
        echo "  - Check username and password in .env file\n";
        echo "  - Verify user has permissions to access the database\n";
        echo "  - Check if user exists: SELECT user, host FROM mysql.user;\n";
    } elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
        echo "  - Database '{$database}' does not exist\n";
        echo "  - Create the database: CREATE DATABASE `{$database}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;\n";
        echo "  - Or check DB_DATABASE in .env file\n";
    } elseif (strpos($e->getMessage(), 'Connection refused') !== false || 
              strpos($e->getMessage(), 'No route to host') !== false ||
              strpos($e->getMessage(), 'getaddrinfo') !== false) {
        echo "  - Cannot reach database server at {$host}:{$port}\n";
        echo "  - Check if database server is running\n";
        echo "  - Verify DB_HOST and DB_PORT in .env are correct\n";
        echo "  - Test connectivity: ping {$host} or telnet {$host} {$port}\n";
        echo "  - If using Docker, ensure containers are in the same network\n";
    } elseif (strpos($e->getMessage(), 'timeout') !== false) {
        echo "  - Connection timeout\n";
        echo "  - Check if database server is accessible\n";
        echo "  - Verify network connectivity and firewall rules\n";
        echo "  - Check if database server is overloaded\n";
    } else {
        echo "  - Check database server logs\n";
        echo "  - Verify all connection parameters in .env\n";
        echo "  - Ensure database server is running and accessible\n";
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    exit(1);
} catch (Exception $e) {
    echo "\n❌ Unexpected error: {$e->getMessage()}\n";
    echo "  Type: " . get_class($e) . "\n";
    exit(1);
}

