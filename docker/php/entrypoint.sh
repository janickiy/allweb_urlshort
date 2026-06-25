#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

if [ "${1:-}" != "apache2-foreground" ]; then
    exec "$@"
fi

if [ ! -f .env ] && [ -f .env.docker ]; then
    cp .env.docker .env
fi

mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache || true

if [ ! -d vendor ]; then
    composer install --prefer-dist --no-interaction
fi

if [ -f artisan ]; then
    if ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
        php artisan key:generate --force --no-interaction
    fi

    php artisan storage:link --force >/dev/null 2>&1 || true
fi

if [ -f package.json ]; then
    if [ ! -d node_modules ]; then
        npm install
    fi

    if [ ! -f public/build/manifest.json ]; then
        npm run build
    fi
fi

exec "$@"
