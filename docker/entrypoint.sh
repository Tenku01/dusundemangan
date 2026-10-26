#!/bin/sh
set -e

echo "==> Setting up storage permissions..."
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "==> Optimizing Laravel cache..."
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

echo "==> Running Database Migrations & Seeds..."
php artisan migrate --force || echo "Warning: Database migration failed. Check DB credentials."
php artisan db:seed --force || echo "Notice: Database seed skipped."

echo "==> Configuring Nginx port..."
export PORT="${PORT:-8000}"
sed -i "s/listen [0-9]*;/listen ${PORT};/g" /etc/nginx/http.d/default.conf

echo "==> Starting Web Server (Nginx & PHP-FPM)..."
exec /usr/bin/supervisord -c /etc/supervisord.conf
