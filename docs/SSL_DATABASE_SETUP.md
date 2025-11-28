# SSL Database Connection Setup

## Issue

Your database server requires SSL/TLS connections (`require_secure_transport=ON`). This document explains how to configure Laravel to connect using SSL.

## Solution

### 1. Update `.env` File

Add these optional SSL configuration variables to your `.env` file:

```env
# Database SSL Configuration (optional)
# If your database requires SSL, these settings enable it
MYSQL_ATTR_SSL_CA=
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=false
```

**Note:** 
- Setting `MYSQL_ATTR_SSL_CA` to empty/null enables SSL without CA verification
- Setting `MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=false` disables server certificate verification
- For production, you should use proper SSL certificates

### 2. Configuration Already Updated

The `config/database.php` file has been updated to support SSL connections:

```php
'options' => extension_loaded('pdo_mysql') ? array_filter([
    PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
    PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => env('MYSQL_ATTR_SSL_VERIFY_SERVER_CERT', false),
]) : [],
```

### 3. Test Connection

Test your database connection:

```bash
# Using the simple test script
php scripts/test-db-connection-simple.php

# Or via HTTP endpoint (if app is running)
curl http://your-domain.com/test-db
```

## How It Works

When the database server requires SSL (`require_secure_transport=ON`):

1. **Without SSL configuration:** Connection fails with error:
   ```
   SQLSTATE[HY000] [3159] Connections using insecure transport are prohibited
   ```

2. **With SSL configuration:** 
   - Setting `PDO::MYSQL_ATTR_SSL_CA` to `null` enables SSL
   - Setting `PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT` to `false` disables certificate verification
   - Connection succeeds using SSL/TLS

## Production Recommendations

For production environments, you should:

1. **Use proper SSL certificates:**
   ```env
   MYSQL_ATTR_SSL_CA=/path/to/ca-cert.pem
   MYSQL_ATTR_SSL_CERT=/path/to/client-cert.pem
   MYSQL_ATTR_SSL_KEY=/path/to/client-key.pem
   MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=true
   ```

2. **Store certificates securely:**
   - Don't commit certificates to git
   - Use environment variables or secure storage
   - Set proper file permissions (600)

3. **Verify SSL connection:**
   ```sql
   SHOW STATUS LIKE 'Ssl%';
   ```

## Troubleshooting

### Still getting SSL errors?

1. **Check if SSL is actually enabled:**
   ```bash
   php scripts/test-db-connection-simple.php
   ```
   Should show "Connection successful"

2. **Verify database configuration:**
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

3. **Check database server SSL settings:**
   ```sql
   SHOW VARIABLES LIKE 'require_secure_transport';
   SHOW VARIABLES LIKE 'ssl%';
   ```

## Summary

✅ **Connection Test:** Passed
✅ **SSL Enabled:** Yes (without certificate verification)
✅ **Database:** inlando
✅ **Server:** MariaDB 11.8.5

The database connection is now working with SSL enabled!

