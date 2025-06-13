##
## ────────────────────────── 1. build-stage: PHP deps ───────────────────────────
##
FROM composer:2 AS composer-deps
WORKDIR /app

# кэшируем зависимости
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev --prefer-dist --optimize-autoloader \
        --no-interaction --no-scripts

##
## ────────────────────────── 2. build-stage: Node/Vite ──────────────────────────
##
FROM node:18 AS node-build
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --omit=dev

# копируем только исходники для фронта
COPY resources/ resources/
COPY vite.config.* ./
RUN npm run build          # public/build выйдет из-под /app

##
## ────────────────────────── 3. runtime-stage ───────────────────────────────────
##
FROM php:8.3-cli

# --- системные библиотеки + то, что было в локальном Dockerfile ----------------
RUN apt-get update && apt-get install -y --no-install-recommends \
        git curl unzip zip ca-certificates \
        build-essential pkg-config libzip-dev libicu-dev libexif-dev \
        libmagickwand-dev imagemagick libpng-dev libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" \
            pdo_mysql pdo_pgsql mbstring exif intl bcmath gd zip \
    # --- Imagick (ветка master — фикс под PHP 8.3) -----------------------------
    && git clone --depth 1 https://github.com/Imagick/imagick.git /usr/src/imagick \
    && cd /usr/src/imagick && phpize && ./configure && make -j"$(nproc)" && make install \
    && docker-php-ext-enable imagick \
    && rm -rf /var/lib/apt/lists/* /usr/src/imagick

# --- Composer (уже есть в первом этапе) ----------------------------------------
COPY --from=composer-deps /usr/bin/composer /usr/bin/composer

# --- node не нужен в рантайме, но Railway иногда ставит пакеты в build-hook -----
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y --no-install-recommends nodejs

WORKDIR /var/www/html

# сначала зависимости — лучше для layer-cache
COPY --from=composer-deps /app/vendor  ./vendor
COPY --from=node-build  /app/public/build  ./public/build

# затем весь остальной код
COPY . .

# --- production оптимизация Laravel -------------------------------------------
RUN php artisan config:cache     \
 && php artisan route:cache      \
 && php artisan view:cache

# Railway выдаёт случайный порт в $PORT. Передаём его Artisan-у.
ENV LOG_CHANNEL=errorlog \
    APP_ENV=production

EXPOSE 8080

CMD php artisan migrate --force && php artisan storage:link \
 && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
