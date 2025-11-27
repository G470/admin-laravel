# MariaDB Docker Quick Reference

Quick commands and tips for working with MariaDB in Docker.

## 🚀 Quick Start

### Start MariaDB Container (docker-compose)
```bash
docker-compose -f docker-compose.example.yml up -d mariadb
```

### Connect to MariaDB
```bash
# From host (if port is exposed)
mysql -h localhost -P 3306 -u laravel_user -p

# From Laravel container
docker exec -it laravel_app mysql -h mariadb -u laravel_user -p laravel

# Direct to MariaDB container
docker exec -it laravel_mariadb mysql -u root -p
```

## 🔧 Environment Variables

### For Laravel (.env)
```env
DB_CONNECTION=mariadb
DB_HOST=mariadb
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=laravel_user
DB_PASSWORD=your_password
```

### For MariaDB Container
```env
MYSQL_ROOT_PASSWORD=root_password
MYSQL_DATABASE=laravel
MYSQL_USER=laravel_user
MYSQL_PASSWORD=laravel_password
```

## 📋 Common Commands

### Database Operations
```bash
# Show databases
docker exec -it laravel_mariadb mysql -u root -p -e "SHOW DATABASES;"

# Show tables
docker exec -it laravel_mariadb mysql -u root -p laravel -e "SHOW TABLES;"

# Create database
docker exec -it laravel_mariadb mysql -u root -p -e "CREATE DATABASE laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Drop database
docker exec -it laravel_mariadb mysql -u root -p -e "DROP DATABASE laravel;"
```

### User Management
```bash
# Create user
docker exec -it laravel_mariadb mysql -u root -p -e "CREATE USER 'laravel_user'@'%' IDENTIFIED BY 'password';"

# Grant privileges
docker exec -it laravel_mariadb mysql -u root -p -e "GRANT ALL PRIVILEGES ON laravel.* TO 'laravel_user'@'%'; FLUSH PRIVILEGES;"

# Show users
docker exec -it laravel_mariadb mysql -u root -p -e "SELECT user, host FROM mysql.user;"

# Change password
docker exec -it laravel_mariadb mysql -u root -p -e "ALTER USER 'laravel_user'@'%' IDENTIFIED BY 'new_password'; FLUSH PRIVILEGES;"
```

### Backup & Restore
```bash
# Backup database
docker exec laravel_mariadb mysqldump -u root -p laravel > backup_$(date +%Y%m%d_%H%M%S).sql

# Restore database
docker exec -i laravel_mariadb mysql -u root -p laravel < backup.sql

# Backup all databases
docker exec laravel_mariadb mysqldump -u root -p --all-databases > all_databases_backup.sql
```

### Laravel Integration
```bash
# Test connection
docker exec -it laravel_app php artisan db:show

# Run migrations
docker exec -it laravel_app php artisan migrate

# Run seeders
docker exec -it laravel_app php artisan db:seed

# Tinker (test connection)
docker exec -it laravel_app php artisan tinker
# Then: DB::connection()->getPdo();
```

## 🔍 Troubleshooting

### Check Container Status
```bash
docker ps | grep mariadb
docker logs laravel_mariadb
docker logs laravel_mariadb --tail 50 -f
```

### Test Network Connectivity
```bash
# Ping from Laravel container
docker exec -it laravel_app ping mariadb

# Test port
docker exec -it laravel_app nc -zv mariadb 3306

# Check network
docker network inspect laravel_network
```

### Connection Issues
```bash
# Check if MariaDB is listening
docker exec -it laravel_mariadb netstat -tlnp | grep 3306

# Check MariaDB process
docker exec -it laravel_mariadb ps aux | grep mysql

# View MariaDB configuration
docker exec -it laravel_mariadb cat /etc/mysql/my.cnf
```

## 📊 Monitoring

### Check Database Size
```sql
SELECT 
    table_schema AS 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'Size (MB)'
FROM information_schema.tables
GROUP BY table_schema;
```

### Show Process List
```sql
SHOW PROCESSLIST;
```

### Check Connections
```sql
SHOW STATUS LIKE 'Threads_connected';
SHOW VARIABLES LIKE 'max_connections';
```

## 🛠️ Maintenance

### Optimize Tables
```sql
OPTIMIZE TABLE table_name;
```

### Repair Tables
```sql
REPAIR TABLE table_name;
```

### Check Table Status
```sql
CHECK TABLE table_name;
```

## 🔐 Security Checklist

- [ ] Changed root password
- [ ] Created dedicated app user (not root)
- [ ] Limited user privileges to specific database
- [ ] Using strong passwords
- [ ] Not exposing port 3306 publicly (unless needed)
- [ ] Regular backups configured
- [ ] Environment variables secured (not in code)

## 📚 Additional Resources

- Full manual: [DOCKER_MARIADB_SETUP.md](./DOCKER_MARIADB_SETUP.md)
- [MariaDB Documentation](https://mariadb.com/kb/en/documentation/)
- [Laravel Database Docs](https://laravel.com/docs/database)

