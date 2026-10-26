#!/bin/sh
# コンテナ起動時に Laravel のキャッシュを作り直してから Apache を起動する
set -e

cd /var/www/html

# storage は volume なので、初回や中身が消えたときのためにディレクトリを用意する
for d in storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs; do
    mkdir -p "$d"
done
chown -R www-data:www-data storage bootstrap/cache

su -s /bin/sh www-data -c '
    php artisan package:discover --ansi
    php artisan config:cache
    php artisan view:cache
    # route:cache はルート名の重複 (api.bookMark.delete) があり使えないので、古いキャッシュだけ消す
    php artisan route:clear
'

exec docker-php-entrypoint "$@"
