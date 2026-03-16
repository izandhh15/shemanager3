#!/bin/bash
set -e

echo "=== SheManager Startup ==="

if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

echo "Running migrations..."
php artisan migrate --force --no-interaction

if [ "${DB_FRESH}" = "true" ]; then
  echo "Fresh seed — wiping DB..."
  php artisan migrate:fresh --force --no-interaction
  echo "Seeding women's football data..."
  php artisan app:seed-reference-data --season=2026 --no-interaction
  echo "Clearing crest cache..."
  rm -rf public/crests/*
  echo "Downloading fresh crests..."
  php artisan app:fetch-team-crests --force
else
  TEAM_COUNT=$(php artisan tinker --execute="echo \App\Models\Team::count();" 2>/dev/null | tail -1 || echo "0")
  if [ "$TEAM_COUNT" = "0" ]; then
    echo "Seeding..."
    php artisan app:seed-reference-data --season=2026 --no-interaction
    rm -rf public/crests/*
    php artisan app:fetch-team-crests --force
  else
    echo "DB has $TEAM_COUNT teams. Skipping seed."
  fi
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan queue:work --sleep=3 --tries=3 --daemon &
php artisan schedule:work &

echo "Starting web server on port ${PORT:-8080}..."
exec php a
