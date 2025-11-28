#!/bin/bash
# Post-create script for devcontainer
# This runs once when the container is first created

set -e

echo "🚀 Setting up Laravel development environment..."

# Navigate to workspace
cd /var/www/html

# Create storage directories if they don't exist
echo "📁 Creating storage directories..."
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set proper permissions
echo "🔐 Setting permissions..."
sudo chown -R vscode:vscode storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache

# Install PHP dependencies (with dev dependencies)
if [ ! -d "vendor" ] || [ ! -f "vendor/autoload.php" ]; then
    echo "📦 Installing Composer dependencies..."
    composer install
else
    echo "✅ Composer dependencies already installed"
fi

# Install Node.js dependencies
if [ ! -d "node_modules" ]; then
    echo "📦 Installing npm dependencies..."
    npm install
else
    echo "✅ npm dependencies already installed"
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "🔑 Generating application key..."
    php artisan key:generate --force
else
    echo "✅ APP_KEY already set"
fi

# Create .env file if it doesn't exist
if [ ! -f ".env" ]; then
    echo "📝 Creating .env file..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        # Update database configuration
        sed -i 's/DB_CONNECTION=.*/DB_CONNECTION=mariadb/' .env
        sed -i 's/DB_HOST=.*/DB_HOST=mariadb/' .env
        sed -i 's/DB_PORT=.*/DB_PORT=3306/' .env
        sed -i 's/DB_DATABASE=.*/DB_DATABASE=laravel/' .env
        sed -i 's/DB_USERNAME=.*/DB_USERNAME=laravel_user/' .env
        sed -i 's/DB_PASSWORD=.*/DB_PASSWORD=laravel_password/' .env
        sed -i 's/APP_ENV=.*/APP_ENV=local/' .env
        sed -i 's/APP_DEBUG=.*/APP_DEBUG=true/' .env
    fi
fi

# Clear and cache config
echo "🧹 Clearing caches..."
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

# Wait for database to be ready
echo "⏳ Waiting for database to be ready..."
timeout=60
counter=0
until php -r "try { new PDO('mysql:host=mariadb;port=3306', 'laravel_user', 'laravel_password'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; do
    if [ $counter -ge $timeout ]; then
        echo "⚠️  Database connection timeout. You may need to run migrations manually."
        break
    fi
    echo "   Waiting for database... ($counter/$timeout)"
    sleep 2
    counter=$((counter + 2))
done

# Run migrations if database is ready
if php -r "try { new PDO('mysql:host=mariadb;port=3306', 'laravel_user', 'laravel_password'); exit(0); } catch (Exception \$e) { exit(1); }" 2>/dev/null; then
    echo "🗄️  Running database migrations..."
    php artisan migrate --force || echo "⚠️  Migration failed or already run"
else
    echo "⚠️  Skipping migrations - database not ready"
fi

echo "✅ Development environment setup complete!"
echo ""
echo "📋 Next steps:"
echo "   1. Start the Laravel server: php artisan serve --host=0.0.0.0 --port=8000"
echo "   2. Start Vite dev server: npm run dev"
echo "   3. Access the app at: http://localhost:8000"
echo ""

