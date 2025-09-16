#!/usr/bin/env sh
set -e

chown -R www-data:www-data /var/www/html
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

export PORT=${PORT:-8080}
mkdir -p /run/nginx

if [ -f /etc/nginx/conf.d/default.conf ]; then
  envsubst '${PORT}' < /etc/nginx/conf.d/default.conf > /etc/nginx/conf.d/default-runtime.conf
  mv /etc/nginx/conf.d/default-runtime.conf /etc/nginx/conf.d/default.conf
fi

nginx -t || { echo "Nginx config test failed"; cat /etc/nginx/conf.d/default.conf; exit 1; }


if [ ! -d vendor ] || [ "$COMPOSER_INSTALL" = "1" ]; then
  composer install --no-interaction --prefer-dist || composer install --no-interaction
fi

php artisan key:generate --force || true



php artisan storage:link || true

exec /usr/bin/supervisord -c /etc/supervisord.conf

