#!/bin/sh
set -e

# Clear stale caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# Rebuild fresh for this environment
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
