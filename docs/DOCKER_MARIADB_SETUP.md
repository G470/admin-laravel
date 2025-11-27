# MariaDB Docker Container Setup Manual

This manual explains how to set up and connect a separate MariaDB Docker container to your Laravel application in the same Docker network, specifically for Coolify deployments.

## Table of Contents

1. [Overview](#overview)
2. [Setting Up MariaDB Container in Coolify](#setting-up-mariadb-container-in-coolify)
3. [Docker Network Configuration](#docker-network-configuration)
4. [Laravel Database Configuration](#laravel-database-configuration)
5. [Connecting to MariaDB Container](#connecting-to-mariadb-container)
6. [Environment Variables](#environment-variables)
7. [Troubleshooting](#troubleshooting)
8. [Manual Connection Commands](#manual-connection-commands)

## Overview

In a Dockerized environment, your Laravel application and MariaDB database should run as separate containers in the same Docker network. This allows them to communicate using container names as hostnames.

**Key Concepts:**
- Containers in the same Docker network can communicate using container names
- MariaDB container name acts as the database hostname
- Port mapping is only needed for external access (not for inter-container communication)

## Setting Up MariaDB Container in Coolify

### Option 1: Using Coolify's Database Service

1. **Create a New Database Service:**
   - In Coolify, go to your project
   - Click "New Resource" → "Database"
   - Select "MariaDB"
   - Choose a version (recommended: `10.11` or `11.0`)

2. **Configure Database:**
   - **Name**: `mariadb` (or your preferred name - this will be the hostname)
   - **Database Name**: `laravel` (or your app's database name)
   - **Username**: `laravel_user` (or your preferred username)
   - **Password**: Set a strong password (save this for Laravel config)
   - **Root Password**: Set a root password (for admin access)

3. **Network Configuration:**
   - Ensure the MariaDB container is in the same Docker network as your Laravel app
   - In Coolify, both services should automatically be in the same network if they're in the same project

### Option 2: Using Docker Compose (Local Development)

Create a `docker-compose.yml` file for local development:

```yaml
version: '3.8'

services:
  mariadb:
    image: mariadb:10.11
    container_name: laravel_mariadb
    restart: unless-stopped
    environment:
      MYSQL_ROOT_PASSWORD: root_password_here
      MYSQL_DATABASE: laravel
      MYSQL_USER: laravel_user
      MYSQL_PASSWORD: laravel_password
    ports:
      - "3306:3306"  # Only needed for external access
    volumes:
      - mariadb_data:/var/lib/mysql
    networks:
      - laravel_network
    healthcheck:
      test: ["CMD", "healthcheck.sh", "--connect", "--innodb_initialized"]
      interval: 10s
      timeout: 5s
      retries: 5

  app:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: laravel_app
    restart: unless-stopped
    depends_on:
      mariadb:
        condition: service_healthy
    networks:
      - laravel_network
    environment:
      DB_CONNECTION: mysql
      DB_HOST: mariadb
      DB_PORT: 3306
      DB_DATABASE: laravel
      DB_USERNAME: laravel_user
      DB_PASSWORD: laravel_password

networks:
  laravel_network:
    driver: bridge

volumes:
  mariadb_data:
```

## Docker Network Configuration

### Understanding Docker Networks

When containers are in the same Docker network, they can communicate using:
- **Container name** as hostname (e.g., `mariadb`)
- **Service name** in docker-compose (e.g., `mariadb`)
- Internal IP addresses (not recommended, use names instead)

### Verifying Network Connection

1. **Check if containers are in the same network:**
   ```bash
   docker network inspect <network_name>
   ```

2. **From Laravel container, test connection:**
   ```bash
   docker exec -it laravel_app ping mariadb
   ```

3. **Test database port:**
   ```bash
   docker exec -it laravel_app nc -zv mariadb 3306
   ```

## Laravel Database Configuration

### Environment Variables

In your Laravel application's `.env` file (or Coolify environment variables), configure:

```env
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=your_secure_password_here
```

**Important Notes:**
- `DB_HOST` should be the **MariaDB container name** (not `localhost` or `127.0.0.1`)
- `DB_PORT` is `3306` (default MariaDB port, no need to change)
- Use the internal container port, not the mapped external port

### Database Configuration File

Your `config/database.php` should already be configured correctly. Verify the MySQL connection:

```php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DB_URL'),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', '3306'),
    'database' => env('DB_DATABASE', 'laravel'),
    'username' => env('DB_USERNAME', 'root'),
    'password' => env('DB_PASSWORD', ''),
    'unix_socket' => env('DB_SOCKET', ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
    'prefix_indexes' => true,
    'strict' => true,
    'engine' => null,
    'options' => extension_loaded('pdo_mysql') ? array_filter([
        PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    ]) : [],
],
```

## Connecting to MariaDB Container

### Method 1: From Laravel Container (Recommended)

1. **Access Laravel container shell:**
   ```bash
   docker exec -it laravel_app sh
   ```

2. **Connect using MySQL client:**
   ```bash
   mysql -h mariadb -u laravel_user -p laravel
   ```
   Enter your password when prompted.

3. **Or connect as root:**
   ```bash
   mysql -h mariadb -u root -p
   ```

### Method 2: From Host Machine

If MariaDB port is exposed (mapped to host):

```bash
mysql -h localhost -P 3306 -u laravel_user -p laravel
```

### Method 3: Using Docker Exec Directly

```bash
docker exec -it laravel_mariadb mysql -u laravel_user -p laravel
```

### Method 4: Using Laravel Artisan Tinker

```bash
docker exec -it laravel_app php artisan tinker
```

Then in tinker:
```php
DB::connection()->getPdo();
// Should return PDO object if connected successfully
```

## Environment Variables

### Required Environment Variables for Laravel

Set these in Coolify's environment variables section or in your `.env` file:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=your_secure_password_here

# Optional: For better performance
DB_PREFIX=
DB_STRICT=true
```

### MariaDB Container Environment Variables

When creating the MariaDB container, set:

```env
MYSQL_ROOT_PASSWORD=root_password_here
MYSQL_DATABASE=laravel
MYSQL_USER=laravel_user
MYSQL_PASSWORD=laravel_password
```

## Troubleshooting

### Issue: "SQLSTATE[HY000] [2002] Connection refused"

**Cause:** Laravel can't reach the MariaDB container.

**Solutions:**
1. Verify containers are in the same network:
   ```bash
   docker network ls
   docker network inspect <network_name>
   ```

2. Check MariaDB container is running:
   ```bash
   docker ps | grep mariadb
   ```

3. Verify DB_HOST matches container name:
   ```bash
   docker ps --format "table {{.Names}}\t{{.Image}}"
   ```
   Use the exact container name as DB_HOST.

4. Test connectivity from Laravel container:
   ```bash
   docker exec -it laravel_app ping mariadb
   docker exec -it laravel_app nc -zv mariadb 3306
   ```

### Issue: "Access denied for user"

**Cause:** Wrong username/password or user doesn't have permissions.

**Solutions:**
1. Verify credentials in MariaDB:
   ```bash
   docker exec -it laravel_mariadb mysql -u root -p
   ```
   Then:
   ```sql
   SELECT user, host FROM mysql.user;
   SHOW GRANTS FOR 'laravel_user'@'%';
   ```

2. Grant proper permissions:
   ```sql
   GRANT ALL PRIVILEGES ON laravel.* TO 'laravel_user'@'%';
   FLUSH PRIVILEGES;
   ```

### Issue: "Unknown database"

**Cause:** Database doesn't exist.

**Solutions:**
1. Create the database:
   ```bash
   docker exec -it laravel_mariadb mysql -u root -p
   ```
   ```sql
   CREATE DATABASE IF NOT EXISTS laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

2. Or run Laravel migrations:
   ```bash
   docker exec -it laravel_app php artisan migrate
   ```

### Issue: Containers can't communicate

**Cause:** Containers are in different networks.

**Solutions:**
1. In Coolify, ensure both services are in the same project/network
2. For docker-compose, verify they share the same network
3. Manually connect container to network:
   ```bash
   docker network connect <network_name> <container_name>
   ```

## Manual Connection Commands

### Quick Reference: Common Commands

```bash
# 1. List all containers
docker ps -a

# 2. Check container networks
docker inspect <container_name> | grep -A 10 Networks

# 3. Connect to MariaDB from Laravel container
docker exec -it laravel_app mysql -h mariadb -u laravel_user -p

# 4. Connect to MariaDB directly
docker exec -it laravel_mariadb mysql -u root -p

# 5. Show databases
docker exec -it laravel_mariadb mysql -u root -p -e "SHOW DATABASES;"

# 6. Show users
docker exec -it laravel_mariadb mysql -u root -p -e "SELECT user, host FROM mysql.user;"

# 7. Test connection from Laravel container
docker exec -it laravel_app php artisan db:show

# 8. Run migrations
docker exec -it laravel_app php artisan migrate

# 9. Check Laravel database connection
docker exec -it laravel_app php artisan tinker
# Then: DB::connection()->getPdo();

# 10. View MariaDB logs
docker logs laravel_mariadb

# 11. View Laravel logs
docker logs laravel_app
```

### SQL Commands for Database Management

```sql
-- Show all databases
SHOW DATABASES;

-- Use a database
USE laravel;

-- Show tables
SHOW TABLES;

-- Show table structure
DESCRIBE table_name;

-- Create a new user (if needed)
CREATE USER 'laravel_user'@'%' IDENTIFIED BY 'password';

-- Grant privileges
GRANT ALL PRIVILEGES ON laravel.* TO 'laravel_user'@'%';
FLUSH PRIVILEGES;

-- Show grants for user
SHOW GRANTS FOR 'laravel_user'@'%';

-- Change user password
ALTER USER 'laravel_user'@'%' IDENTIFIED BY 'new_password';
FLUSH PRIVILEGES;
```

## Best Practices

1. **Security:**
   - Use strong passwords
   - Don't use root user for application connections
   - Limit user privileges to specific database
   - Use environment variables, never hardcode credentials

2. **Performance:**
   - Use connection pooling
   - Configure proper charset (utf8mb4)
   - Enable query caching if needed
   - Monitor slow queries

3. **Backup:**
   ```bash
   # Backup database
   docker exec laravel_mariadb mysqldump -u root -p laravel > backup.sql
   
   # Restore database
   docker exec -i laravel_mariadb mysql -u root -p laravel < backup.sql
   ```

4. **Monitoring:**
   - Check container health: `docker ps`
   - Monitor logs: `docker logs -f laravel_mariadb`
   - Check network: `docker network inspect <network_name>`

## Coolify-Specific Notes

When using Coolify:

1. **Database Service:**
   - Coolify automatically creates databases in the same network
   - Use the service name as DB_HOST (usually the database service name)
   - Environment variables are managed in Coolify's UI

2. **Connection String:**
   - Coolify may provide a connection string - extract host, port, database, username, password
   - The host will be the database service name

3. **Health Checks:**
   - Coolify monitors database health
   - Ensure health checks are passing before deploying Laravel app

4. **Backups:**
   - Configure automated backups in Coolify
   - Test restore procedures regularly

## Additional Resources

- [MariaDB Docker Hub](https://hub.docker.com/_/mariadb)
- [Laravel Database Documentation](https://laravel.com/docs/database)
- [Docker Networking](https://docs.docker.com/network/)
- [Coolify Documentation](https://coolify.io/docs)

---

**Last Updated:** 2025-11-26

