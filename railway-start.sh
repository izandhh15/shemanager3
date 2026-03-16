#!/bin/bash
set -e

echo "=== SheManager Startup ==="

if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

echo "Running migrations..."
php artisan migrate --force --no-interaction

if [ "${DB_FRESH}" = "true" ]; then
  echo "Fresh seed requested..."
  php artisan migrate:fresh --force --no-interaction
  php artisan app:seed-reference-data --season=2026 --no-interaction
elif php artisan tinker --execute="echo \App\Models\Team::count();" 2>/dev/null | tail -1 | grep -q "^0$"; then
  echo "Empty DB — seeding..."
  php artisan app:seed-reference-data --season=2026 --no-interaction
else
  echo "DB already seeded. Skipping."
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:work --sleep=3 --tries=3 --daemon &
php artisan schedule:work &

echo "Starting web server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
