#!/bin/sh
set -e

echo "Setting up database..."
mkdir -p /app/database
touch /app/database/database.sqlite

chown -R www-data:www-data /app/database
chmod -R 775 /app/database

echo "Running migrations..."
php artisan migrate:fresh --force

echo "Running seeder"
php artisan db:seed --force

# echo "Generating cache"
# php artisan config:cache && \
# php artisan route:cache && \
# php artisan view:cache

exec "$@"