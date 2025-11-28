#!/bin/bash
# Post-start script for devcontainer
# This runs every time the container starts

set -e

echo "🔄 Starting development services..."

cd /var/www/html

# Ensure storage directories exist and have correct permissions (sh doesn't support brace expansion)
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/framework/cache
mkdir -p storage/logs
mkdir -p bootstrap/cache
sudo chown -R vscode:vscode storage bootstrap/cache 2>/dev/null || true
sudo chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Clear caches on startup (for development)
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true

echo "✅ Development environment ready!"

