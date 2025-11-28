#!/bin/sh
# Entrypoint script to ensure required directories exist and APP_KEY is set

# Create storage directories if they don't exist (sh doesn't support brace expansion)
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Check if APP_KEY is set as environment variable
if [ -z "$APP_KEY" ]; then
    # Check if .env file exists and has APP_KEY
    if [ -f /var/www/html/.env ]; then
        # Check if APP_KEY exists in .env but might be empty
        if ! grep -q "^APP_KEY=base64:" /var/www/html/.env 2>/dev/null && ! grep -q "^APP_KEY=" /var/www/html/.env 2>/dev/null; then
            echo "APP_KEY not found in .env file. Generating application key..."
            cd /var/www/html
            php artisan key:generate --force --no-interaction 2>/dev/null || {
                echo "ERROR: Failed to generate APP_KEY."
                echo "Please set APP_KEY as an environment variable in your deployment platform (e.g., Coolify)."
                echo "You can generate a key by running: php artisan key:generate"
            }
        fi
    else
        echo "WARNING: APP_KEY environment variable is not set and .env file not found."
        echo "Please set APP_KEY as an environment variable in your deployment platform (e.g., Coolify)."
        echo "To generate a key, run: php artisan key:generate"
    fi
fi

# Execute the original command
exec "$@"

