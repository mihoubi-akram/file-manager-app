#!/bin/bash
set -e

# Copy .env if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
fi

# Install dependencies if vendor folder is missing
if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

# Generate app key if not set
if grep -q "APP_KEY=$" .env; then
    php artisan key:generate --no-interaction
fi

# Run migrations only when explicitly requested
if [ "${RUN_MIGRATIONS}" = "true" ]; then
    php artisan migrate --no-interaction --force
fi

# Start Laravel server
php artisan serve --host=0.0.0.0 --port=8000
