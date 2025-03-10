# PHP 8.2-FPM ベースイメージを使用
FROM php:8.2-fpm

# 必要なパッケージをインストール
RUN apt-get update && apt-get install -y \
  libpq-dev \
  zip \
  unzip \
  curl \
  git \
  && docker-php-ext-install pdo pdo_pgsql

# Composer のインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 作業ディレクトリを指定
WORKDIR /var/www

# Laravel の権限設定（キャッシュ・ストレージ）
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# PHP 設定ファイルを適用
COPY ./docker/php/php.ini /usr/local/etc/php/php.ini

# コンテナ起動時に PHP-FPM を実行
CMD ["php-fpm"]
