#!/bin/bash
set -e

echo "=== SheManager Startup ==="

if [ -z "$APP_KEY" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

echo "Running migrations..."
php artisan migrate --force --no-interaction

echo "Seeding women's football data (2026 season)..."
php artisan app:seed-reference-data --season=2026 --no-interaction

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:work --sleep=3 --tries=3 --daemon &
php artisan schedule:work &

echo "Starting web server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
