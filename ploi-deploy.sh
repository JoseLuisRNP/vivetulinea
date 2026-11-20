#!/bin/bash

set -e
# Pull changes

git pull origin main

# Build app

composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

echo "" | sudo -S service php8.4-fpm reload

npm ci
export NODE_OPTIONS="--max-old-space-size=2048"  # Cambia 2048 a un valor adecuado según tu memoria disponible

npm run build
rm -rf ./node_modules

# Prepare Laravel

php artisan migrate --force --seed
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache

# Prepare Storage

rm -rf ./public/storage
php artisan storage:link

echo "🚀 Application deployed!"
