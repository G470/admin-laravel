# Multi-stage build for Laravel application optimized for Coolify

# Stage 1: Node.js - Build frontend assets
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copy package files (including package-lock.json for npm ci)
COPY package*.json ./

# Install dependencies (npm ci installs all dependencies including dev by default)
RUN npm ci

# Copy frontend resources
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

# Build assets
RUN npm run build

# Stage 2: Composer - Install PHP dependencies
FROM composer:2.7 AS composer-builder

WORKDIR /app

# Copy composer files and application code needed for autoloader
COPY composer*.json ./
COPY app ./app
COPY database ./database
COPY bootstrap ./bootstrap
COPY artisan ./artisan
COPY config ./config

# Install dependencies (no dev dependencies for production)
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

# Generate optimized autoloader (skip scripts to avoid artisan dependency)
RUN composer dump-autoload --optimize --classmap-authoritative --no-dev --no-scripts

# Stage 3: PHP-FPM - Production image
FROM php:8.2-fpm-alpine AS production

# Install system dependencies and PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    postgresql-dev \
    icu-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    zip \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    intl \
    opcache

# Install Redis extension
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev $PHPIZE_DEPS

# Configure PHP
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory.ini \
    && echo "upload_max_filesize=100M" > /usr/local/etc/php/conf.d/upload.ini \
    && echo "post_max_size=100M" >> /usr/local/etc/php/conf.d/upload.ini \
    && echo "max_execution_time=300" > /usr/local/etc/php/conf.d/execution.ini

# Configure PHP-FPM to listen on TCP port 9000
RUN sed -i 's/listen = .*/listen = 127.0.0.1:9000/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/;listen.owner = .*/listen.owner = www-data/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/;listen.group = .*/listen.group = www-data/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/user = .*/user = www-data/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/group = .*/group = www-data/' /usr/local/etc/php-fpm.d/www.conf

# Configure OPcache for production
RUN echo "opcache.enable=1" > /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files first (vendor and public/build are excluded via .dockerignore)
COPY --chown=www-data:www-data . .

# Copy vendor from composer-builder (overwrites if exists)
COPY --from=composer-builder --chown=www-data:www-data /app/vendor ./vendor

# Copy built assets from node-builder (overwrites if exists)
COPY --from=node-builder --chown=www-data:www-data /app/public/build ./public/build

# Create necessary directories and set permissions
RUN mkdir -p storage/framework/{sessions,views,cache} \
    storage/logs \
    bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Configure Nginx
COPY docker/nginx.conf /etc/nginx/http.d/default.conf

# Configure Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy and make healthcheck script executable
COPY docker/healthcheck.sh /usr/local/bin/healthcheck.sh
RUN chmod +x /usr/local/bin/healthcheck.sh

# Copy and make entrypoint script executable
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Expose port
EXPOSE 80

# Health check - check if services are running
HEALTHCHECK --interval=30s --timeout=5s --start-period=60s --retries=3 \
    CMD /usr/local/bin/healthcheck.sh

# Set entrypoint to ensure directories exist at runtime
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

