#!/bin/bash
set -e

if [ ! -f .env ]; then
  cat > .env << ENVEOF
APP_NAME="Sistema Hospedaje"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://${RAILWAY_PUBLIC_DOMAIN:-localhost}/demos/hospedaje

DB_CONNECTION=mysql
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-demo_hospedaje}
DB_USERNAME=${DB_USER:-root}
DB_PASSWORD=${DB_PASSWORD:-}

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
SESSION_COOKIE=hospedaje_session
LOG_CHANNEL=stderr
ENVEOF
fi

php artisan key:generate --force 2>&1 || true
php artisan migrate --force 2>&1 || true
php artisan db:seed --force 2>&1 || true
php artisan storage:link 2>&1 || true
php artisan config:clear 2>&1 || true
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

exec php artisan serve --host=127.0.0.1 --port=${DEMO_PORT:-5217}