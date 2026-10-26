# syntax=docker/dockerfile:1
# my-wiki 本番用イメージ
# ビルドはリポジトリ直下をコンテキストにする: docker compose build

# --- composer: PHP の依存パッケージ ---
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --no-interaction --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

# --- node: Vite でフロントをビルド ---
# resources/js/app.js が vendor/tightenco/ziggy を import しているので vendor の後に行う
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js postcss.config.js tailwind.config.js jsconfig.json ./
COPY resources resources
COPY --from=vendor /app/vendor/tightenco vendor/tightenco
COPY --from=vendor /app/vendor/laravel/framework/src/Illuminate/Pagination/resources vendor/laravel/framework/src/Illuminate/Pagination/resources
RUN npm run build

# --- 実行環境 ---
# PHP を上げるときはここのタグを変える（Laravel 9 は公式には 8.2 まで）
FROM php:8.2-apache

COPY --from=mlocati/php-extension-installer:2 /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql opcache zip intl bcmath

RUN a2enmod rewrite headers remoteip \
    && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"
COPY deploy/php.ini "$PHP_INI_DIR/conf.d/zz-my-wiki.ini"
COPY deploy/apache.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html
COPY --chown=www-data:www-data . .
COPY --from=vendor --chown=www-data:www-data /app/vendor vendor
COPY --from=assets --chown=www-data:www-data /app/public/build public/build

COPY deploy/entrypoint.sh /usr/local/bin/my-wiki-entrypoint
ENTRYPOINT ["my-wiki-entrypoint"]
CMD ["apache2-foreground"]
