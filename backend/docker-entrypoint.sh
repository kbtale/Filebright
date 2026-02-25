#!/bin/sh
set -e

# Fix permissions for storage and bootstrap/cache
# These are often mounted as volumes and need to be writable by www-data
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Run optimizations and migrations
php artisan optimize
php artisan migrate --force

# Execute the main command
exec "$@"
