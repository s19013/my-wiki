#!/bin/sh
git pull
npm install
composer install
php artisan migrate
npm run build
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# apacheの再起動はapachenの設定ファイルをいじった時で十分
# sudo service apache2 restart a
