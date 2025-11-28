# Database Connection Testing

This document explains how to test your database connection in the Docker container.

## Methods

### Method 1: HTTP Endpoint (Easiest)

Access the database test endpoint via HTTP:

```bash
curl http://your-domain.com/test-db
```

Or in your browser:
```
http://your-domain.com/test-db
```

**Response Example (Success):**
```json
{
  "status": "success",
  "timestamp": "2025-11-27T14:00:00.000000Z",
  "config": {
    "driver": "mysql",
    "host": "mariadb",
    "port": "3306",
    "database": "laravel",
    "username": "laravel_user"
  },
  "connection": {
    "status": "success",
    "message": "Database connection successful",
    "connection_time_ms": 12.5
  },
  "query_test": {
    "status": "success",
    "message": "Query executed successfully",
    "mysql_version": "10.11.2-MariaDB",
    "current_database": "laravel",
    "current_user": "laravel_user@%"
  },
  "tables": {
    "status": "success",
    "count": 25,
    "message": "Found 25 table(s)"
  },
  "message": "Database connection test completed successfully"
}
```

**Response Example (Failure):**
```json
{
  "status": "error",
  "timestamp": "2025-11-27T14:00:00.000000Z",
  "config": {
    "driver": "mysql",
    "host": "mariadb",
    "port": "3306",
    "database": "laravel",
    "username": "laravel_user"
  },
  "error": {
    "code": 2002,
    "message": "SQLSTATE[HY000] [2002] Connection refused",
    "type": "PDOException"
  },
  "message": "Database connection failed"
}
```

### Method 2: Command Line Script

Run the PHP script directly:

```bash
# From inside the container
docker exec -it <container-name> php /var/www/html/scripts/test-db-connection.php

# Or if you have access to the container shell
php scripts/test-db-connection.php
```

**Output Example (Success):**
```
🔍 Testing Database Connection...
==================================================

Configuration:
  Driver:   mysql
  Host:     mariadb
  Port:     3306
  Database: laravel
  Username: laravel_user
  Password: ********

Attempting connection...
✅ Connection successful! (took 12.5ms)

Testing query execution...
✅ Query executed successfully!

Database Information:
  MySQL Version:  10.11.2-MariaDB
  Current DB:     laravel
  Current User:   laravel_user@%

Testing table access...
✅ Found 25 table(s) in database
  Sample tables: users, rentals, categories, bookings, reviews ...

==================================================
✅ All tests passed! Database connection is working.
```

**Output Example (Failure):**
```
🔍 Testing Database Connection...
==================================================

Configuration:
  Driver:   mysql
  Host:     mariadb
  Port:     3306
  Database: laravel
  Username: laravel_user
  Password: ********

Attempting connection...

❌ Connection failed!

Error Details:
  Code:    2002
  Message: SQLSTATE[HY000] [2002] Connection refused

Troubleshooting:
  - Cannot reach database server at mariadb:3306
  - Check if database container is running
  - Verify DB_HOST and DB_PORT are correct
  - Ensure containers are in the same Docker network

==================================================
```

### Method 3: Laravel Artisan Tinker

Use Laravel's tinker to test the connection:

```bash
docker exec -it <container-name> php artisan tinker
```

Then in tinker:
```php
DB::connection()->getPdo();
// Should return: PDO {#1234 ...}

DB::select('SELECT VERSION()');
// Should return database version
```

### Method 4: Direct MySQL Client

If you have mysql client installed in the container:

```bash
docker exec -it <container-name> mysql -h mariadb -u laravel_user -p laravel
```

## Troubleshooting

### Error: "Connection refused"

**Causes:**
- Database container is not running
- Wrong host/port in environment variables
- Containers are not in the same Docker network

**Solutions:**
1. Check if MariaDB container is running:
   ```bash
   docker ps | grep mariadb
   ```

2. Verify environment variables in Coolify:
   ```env
   DB_HOST=mariadb  # Should be the container name
   DB_PORT=3306
   ```

3. Test network connectivity:
   ```bash
   docker exec -it <app-container> ping mariadb
   docker exec -it <app-container> nc -zv mariadb 3306
   ```

### Error: "Access denied"

**Causes:**
- Wrong username or password
- User doesn't have permissions

**Solutions:**
1. Verify credentials in Coolify environment variables
2. Check MariaDB user permissions:
   ```bash
   docker exec -it <mariadb-container> mysql -u root -p
   ```
   Then:
   ```sql
   SELECT user, host FROM mysql.user;
   SHOW GRANTS FOR 'laravel_user'@'%';
   ```

### Error: "Unknown database"

**Causes:**
- Database doesn't exist
- Wrong database name in environment

**Solutions:**
1. Create the database:
   ```bash
   docker exec -it <mariadb-container> mysql -u root -p
   ```
   ```sql
   CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Or run migrations:
   ```bash
   docker exec -it <app-container> php artisan migrate
   ```

## Quick Test Commands

```bash
# Test via HTTP
curl http://your-domain.com/test-db | jq

# Test via script
docker exec -it <container-name> php /var/www/html/scripts/test-db-connection.php

# Test via tinker
docker exec -it <container-name> php artisan tinker
# Then: DB::connection()->getPdo();

# Test network connectivity
docker exec -it <container-name> ping mariadb
docker exec -it <container-name> nc -zv mariadb 3306
```

## Security Note

⚠️ **Important:** The `/test-db` endpoint exposes database configuration information. Consider:
- Removing it in production
- Adding authentication/authorization
- Restricting access by IP
- Using it only for debugging

You can remove the route or add middleware in `routes/web.php`:
```php
Route::get('/test-db', [DatabaseTestController::class, 'test'])
    ->middleware('auth')
    ->name('test.db');
```

