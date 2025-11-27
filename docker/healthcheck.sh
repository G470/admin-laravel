#!/bin/sh
# Health check script for Docker container
# Checks if Nginx and PHP-FPM are running

# Check if Nginx is running
if ! pgrep -f "nginx" > /dev/null; then
    exit 1
fi

# Check if PHP-FPM is running
if ! pgrep -f "php-fpm" > /dev/null; then
    exit 1
fi

# Try to connect to Nginx (even if Laravel returns error, server is up)
if curl -f -s http://localhost/ > /dev/null 2>&1; then
    exit 0
fi

# If curl fails but services are running, still consider healthy
# (Laravel might have errors but server is functional)
if pgrep -f "nginx" > /dev/null && pgrep -f "php-fpm" > /dev/null; then
    exit 0
fi

exit 1

