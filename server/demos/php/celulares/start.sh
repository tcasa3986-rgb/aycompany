#!/bin/bash

echo "=== CRM Tienda Celulares - Modo Demo ==="

if [ ! -f .env ]; then
  cat > .env << ENVEOF
APP_NAME="CRM Tienda Celulares"
APP_ENV=production
APP_KEY=${APP_KEY:-base64:hFEYuEAoYvMGvm/By2qu8NGw1juZY2CqGRY1kuokrQs=}
APP_DEBUG=false
APP_URL=https://${RAILWAY_PUBLIC_DOMAIN:-localhost}/demos/celulares
APP_TIMEZONE=America/Bogota
APP_LOCALE=es
LOG_CHANNEL=stderr
LOG_LEVEL=error
DB_CONNECTION=mysql
DB_HOST=${DB_HOST:-127.0.0.1}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-demo_celulares}
DB_USERNAME=${DB_USER:-root}
DB_PASSWORD=${DB_PASSWORD:-}
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_COOKIE=celulares_session
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=local
ENVEOF
  echo ".env generado."
fi

echo "Configurando base de datos..."
php setup_db.php

echo "Ejecutando migraciones pendientes..."
php artisan migrate --force 2>&1 || true

echo "Enlazando storage..."
php artisan storage:link 2>&1 || true

echo "Cacheando configuracion..."
php artisan config:clear 2>&1 || true
php artisan config:cache 2>&1 || true
php artisan route:cache 2>&1 || true
php artisan view:cache 2>&1 || true

echo "Iniciando CRM Tienda Celulares en puerto ${DEMO_PORT:-5211}..."
exec php artisan serve --host=127.0.0.1 --port=${DEMO_PORT:-5211}