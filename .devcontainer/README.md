# DevContainer Setup

This devcontainer configuration replicates your Coolify production environment for local development.

## Features

- **PHP 8.2** with all extensions matching production
- **MariaDB 10.11** database (same as Coolify)
- **Node.js & npm** for frontend asset building
- **Composer** for PHP dependencies
- **Same environment variables** as Coolify setup
- **Automatic setup** on first container creation

## Getting Started

1. **Open in VS Code/Cursor:**
   - Open the project folder
   - VS Code/Cursor will detect the `.devcontainer` folder
   - Click "Reopen in Container" when prompted

2. **First Time Setup:**
   - The container will automatically:
     - Install Composer dependencies
     - Install npm dependencies
     - Generate APP_KEY if needed
     - Create .env file with correct database settings
     - Run database migrations

3. **Start Development Servers:**
   ```bash
   # Terminal 1: Laravel server
   php artisan serve --host=0.0.0.0 --port=8000
   
   # Terminal 2: Vite dev server (for hot reloading)
   npm run dev
   ```

4. **Access the Application:**
   - Laravel: http://localhost:8000
   - Vite Dev Server: http://localhost:5173

## Database Access

The MariaDB container is automatically started and configured:

- **Host:** `mariadb` (from within container) or `localhost` (from host)
- **Port:** `3306`
- **Database:** `laravel`
- **Username:** `laravel_user`
- **Password:** `laravel_password`
- **Root Password:** `root_password`

### Connect from Host Machine:
```bash
mysql -h localhost -P 3306 -u laravel_user -p laravel
# Password: laravel_password
```

### Connect from Container:
```bash
mysql -h mariadb -u laravel_user -p laravel
# Password: laravel_password
```

## Environment Variables

The devcontainer uses the same environment variables as your Coolify setup:

- `DB_CONNECTION=mariadb`
- `DB_HOST=mariadb`
- `DB_PORT=3306`
- `DB_DATABASE=laravel`
- `DB_USERNAME=laravel_user`
- `DB_PASSWORD=laravel_password`
- `APP_ENV=local`
- `APP_DEBUG=true`

You can override these in `.devcontainer/devcontainer.json` or set them in your local environment.

## Ports

- **8000:** Laravel application (forwarded automatically)
- **5173:** Vite dev server (forwarded automatically)
- **3306:** MariaDB (forwarded automatically)

## Troubleshooting

### Database Connection Issues

If you see database connection errors:

1. Check if MariaDB container is running:
   ```bash
   docker ps | grep mariadb
   ```

2. Test connection from container:
   ```bash
   php artisan db:show
   ```

3. Check database logs:
   ```bash
   docker logs admin-laravel-mariadb
   ```

### Permission Issues

If you encounter permission errors:

```bash
sudo chown -R vscode:vscode storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Rebuild Container

To rebuild the container with fresh setup:

1. Command Palette (Cmd/Ctrl + Shift + P)
2. Select "Dev Containers: Rebuild Container"

## Differences from Production

While this setup matches Coolify, there are some development-specific differences:

- **OPcache timestamps enabled** (for hot reloading)
- **Dev dependencies included** (Composer and npm)
- **Debug mode enabled** (`APP_DEBUG=true`)
- **Local environment** (`APP_ENV=local`)
- **No Nginx/Supervisor** (using PHP built-in server for simplicity)

## VS Code Extensions

The devcontainer automatically installs recommended extensions:

- PHP Intelephense
- Laravel Extras
- Laravel Blade
- Laravel Artisan
- Tailwind CSS IntelliSense
- Docker
- ESLint
- Prettier

## Manual Commands

If you need to run setup commands manually:

```bash
# Install dependencies
composer install
npm install

# Generate key
php artisan key:generate

# Run migrations
php artisan migrate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

