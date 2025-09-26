#!/usr/bin/env bash
set -e

echo ">> Composer install (no-dev)"
composer install --no-dev --prefer-dist --no-progress --no-interaction

echo ">> Generar APP_KEY si falta"
php -r "file_exists('.env') || copy('.env.example','.env');"
php artisan key:generate --force || true

echo ">> Storage link"
php artisan storage:link || true

echo ">> Caches de Laravel"
php artisan config:cache
php artisan route:cache
php artisan view:cache
# También puedes usar: php artisan optimize

echo ">> Migraciones (forzadas)"
php artisan migrate --force || true

echo ">> Iniciando Supervisor (php-fpm + nginx)"
exec /usr/bin/supervisord -c /etc/supervisord.conf
