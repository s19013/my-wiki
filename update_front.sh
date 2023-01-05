#!/bin/sh
git pull
npm install
npm run build
php artisan view:clear
