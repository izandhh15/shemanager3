#!/bin/bash
set -e

echo "=== SheManager Startup ==="

# Generate app key if not set
if [ -z "$APP_KEY" ]; then
  echo "Generating APP_KEY..."
  php artisan key:generate --force
fi

# Run migrations
echo "Running migrations..."
php artisan migrate --force --no-interaction

# Seed reference data (only if DB is empty)
TEAM_COUNT=$(php artisan tinker --execute="echo \App\Models\Team::count();" 2>/dev/null | tail -1 || echo "0")
if [ "$TEAM_COUNT" = "0" ]; then
  echo "Seeding women's football data (2026 season)..."
  php artisan app:seed-reference-data --season=2026 --no-interaction
  echo "Seeding complete."
else
  echo "Database already seeded ($TEAM_COUNT teams). Skipping."
fi

# Cache config, routes and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start queue worker in background
php artisan queue:work --sleep=3 --tries=3 --daemon &

# Start scheduler in background  
php artisan schedule:work &

echo "Starting web server on port ${PORT:-8080}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
