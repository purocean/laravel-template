#!/bin/sh

cd `dirname $0`
php artisan down
git pull && composer install --optimize-autoloader --no-dev \
    && php artisan config:cache --no-interaction \
    && php artisan migrate --no-interaction --force \
    && php artisan api:cache --no-interaction 2>&1
php artisan up
