#!/bin/bash

composer install --no-dev --ignore-platform-reqs

cp .env.example .env

read -p "APP_ENV (local|production, default: proportion): " ans
if [ -z "$ans" ]
then
    ans='proportion'
fi
sed -i "s/^APP_ENV=.*$/APP_ENV=$ans/" .env

read -p "APP_DEBUG (true|false, default: false): " ans
if [ -z "$ans" ]
then
    ans='false'
fi
sed -i "s/^APP_DEBUG=.*$/APP_DEBUG=$ans/" .env

read -p "APP_URL (default: http://localhost): " ans
if [ -z "$ans" ]
then
    ans='http://localhost'
fi
sed -i "s~^APP_URL=.*$~APP_URL=${ans}~" .env

read -p "DB_HOST (default: 127.0.0.1): " ans
if [ -z "$ans" ]
then
    ans='127.0.0.1'
fi
sed -i "s/^DB_HOST=.*$/DB_HOST=$ans/" .env

read -p "DB_PORT (default: 3306): " ans
if [ -z "$ans" ]
then
    ans='3306'
fi
sed -i "s/^DB_PORT=.*$/DB_PORT=$ans/" .env

read -p "DB_DATABASE (default: homestead): " ans
if [ -z "$ans" ]
then
    ans='homestead'
fi
sed -i "s/^DB_DATABASE=.*$/DB_DATABASE=$ans/" .env

read -p "DB_USERNAME (default: homestead): " ans
if [ -z "$ans" ]
then
    ans='homestead'
fi
sed -i "s/^DB_USERNAME=.*$/DB_USERNAME=$ans/" .env

read -sp "DB_PASSWORD (default: secret): " ans
echo
if [ -z "$ans" ]
then
    ans='secret'
fi
sed -i "s/^DB_PASSWORD=.*$/DB_PASSWORD=$ans/" .env

read -p "MONGODB_HOST (default: localhost): " ans
if [ -z "$ans" ]
then
    ans='localhost'
fi
sed -i "s/^MONGODB_HOST=.*$/MONGODB_HOST=$ans/" .env

read -p "MONGODB_PORT (default: 27017): " ans
if [ -z "$ans" ]
then
    ans='27017'
fi
sed -i "s/^MONGODB_PORT=.*$/MONGODB_PORT=$ans/" .env

read -p "MONGODB_DATABASE (default: laravel_template): " ans
if [ -z "$ans" ]
then
    ans='laravel_template'
fi
sed -i "s/^MONGODB_DATABASE=.*$/MONGODB_DATABASE=$ans/" .env

read -p "CACHE_DRIVER (array|file|..., default: redis): " ans
if [ -z "$ans" ]
then
    ans='redis'
fi
sed -i "s/^CACHE_DRIVER=.*$/CACHE_DRIVER=$ans/" .env

read -p "CACHE_PREFIX (default: laravel_template): " ans
if [ -z "$ans" ]
then
    ans='laravel_template'
fi
sed -i "s/^CACHE_PREFIX=.*$/CACHE_PREFIX=$ans/" .env

read -p "QUEUE_DRIVER (sync|redis|..., default: redis): " ans
if [ -z "$ans" ]
then
    ans='redis'
fi
sed -i "s/^QUEUE_DRIVER=.*$/QUEUE_DRIVER=$ans/" .env

read -p "API_DEBUG (true|false, default: false): " ans
if [ -z "$ans" ]
then
    ans='false'
fi
sed -i "s/^API_DEBUG=.*$/API_DEBUG=$ans/" .env

read -p "QYWX_ROOTID (default: 1): " ans
if [ -z "$ans" ]
then
    ans='1'
fi
sed -i "s/^QYWX_ROOTID=.*$/QYWX_ROOTID=$ans/" .env

read -p "QYWX_CORPID: " ans
sed -i "s/^QYWX_CORPID=.*$/QYWX_CORPID=$ans/" .env

read -p "QYWX_CONTACTS_SECRET: " ans
sed -i "s/^QYWX_CONTACTS_SECRET=.*$/QYWX_CONTACTS_SECRET=$ans/" .env

read -p "QYWX_SECRET: " ans
sed -i "s/^QYWX_SECRET=.*$/QYWX_SECRET=$ans/" .env

read -p "QYWX_APPID: " ans
sed -i "s/^QYWX_APPID=.*$/QYWX_APPID=$ans/" .env

php ./artisan key:generate --force
php ./artisan jwt:secret --force
php ./artisan migrate --force

read -p "suadmin password:" ans
if [ ! -z "$ans" ]
then
    php ./artisan rbac:resetpwd suadmin "$ans"
fi
