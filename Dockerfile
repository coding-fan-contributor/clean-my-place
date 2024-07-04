FROM ubuntu:18.04
FROM php:8.2-cli
RUN apt-get update -y && apt-get install -y openssl zip unzip git
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
# RUN docker-php-ext-install -j mbstring
WORKDIR /app
COPY . /app
RUN composer update -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist --ignore-platform-reqs
CMD php artisan serve --host=0.0.0.0 --port=8181
EXPOSE 8181