FROM php:7.4-fpm

RUN apt-get update

RUN apt-get update && apt-get install -y \
libpng-dev \
libzip-dev \
libwebp-dev \
libjpeg62-turbo-dev \
libpng-dev libxpm-dev \
libfreetype6-dev \
imagemagick \
inkscape

RUN docker-php-ext-configure gd
#    --with-gd \
#    --with-webp-dir \
#    --with-jpeg-dir \
#    --with-png-dir \
#    --with-zlib-dir \
#    --with-xpm-dir \
#    --with-freetype-dir
    # --enable-gd-native-ttf

RUN docker-php-ext-install zip gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

ENV COMPOSER_ALLOW_SUPERUSER=1
