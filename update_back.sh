#!/bin/sh
git pull
composer install
php artisan migrate
php artisan cache:clear
php artisan config:clear
php artisan route:clear
