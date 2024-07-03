FROM php:8.2.12
RUN apt-get update -y && apt-get install -y openssl zip unzip git \
&& apt-get clean \
&& pecl install redis \
&& docker-php-ext-configure gd \
&& docker-php-ext-configure zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install mbstring \
gd \
exif \
opcache \
pdo_mysql \
pdo_pgsql \
pgsql \
pcntl \
zip
WORKDIR /app
COPY . /app
RUN composer update -q --no-ansi --no-interaction --no-scripts --no-progress --prefer-dist --ignore-platform-reqs

CMD php artisan serve --host=0.0.0.0 --port=8181
EXPOSE 8181