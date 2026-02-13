#!/bin/sh
set -e

# Create the storage symlink (CRITICAL for images)
php artisan storage:link || true

# Clear stale caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Rebuild fresh for this environment
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
