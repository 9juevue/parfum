FROM php:8.3-fpm

# ------------------------------------------------------------------------------
# 1) Системные пакеты + dev-библиотеки
# ------------------------------------------------------------------------------

RUN apt-get update && apt-get install -y \
    git wget curl unzip ca-certificates \
    build-essential pkg-config \
    libzip-dev libicu-dev libexif-dev \
    libmagickwand-dev imagemagick \
 && rm -rf /var/lib/apt/lists/*

# ------------------------------------------------------------------------------
# 2) Сборка и активация Imagick из ветки master (фикс для PHP 8.3)
# ------------------------------------------------------------------------------

RUN git clone --depth 1 https://github.com/Imagick/imagick.git /usr/src/imagick \
 && cd /usr/src/imagick \
 && phpize \
 && ./configure \
 && make -j"$(nproc)" \
 && make install \
 && docker-php-ext-enable imagick \
 && rm -rf /usr/src/imagick

# ------------------------------------------------------------------------------
# 3) PHP-модули, нужные проекту
# ------------------------------------------------------------------------------

RUN docker-php-ext-configure intl \
 && docker-php-ext-install -j"$(nproc)" \
        intl mysqli exif pdo pdo_mysql zip bcmath \
 && docker-php-ext-enable pdo_mysql zip bcmath

# ------------------------------------------------------------------------------
# 4) Composer и php.ini
# ------------------------------------------------------------------------------

RUN curl -sS https://getcomposer.org/installer | php -- --quiet \
 && mv composer.phar /usr/local/bin/composer

RUN cp "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"
COPY docker/php/conf.d/* "$PHP_INI_DIR/conf.d/"

CMD ["php-fpm"]
