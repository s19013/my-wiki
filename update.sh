#!/bin/sh
git pull
npm run build
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
sudo service apache2 restart
