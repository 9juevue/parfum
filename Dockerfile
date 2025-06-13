######################## 1. deps-stage (Composer) ########################
FROM composer:2 AS composer-deps
WORKDIR /app

# Копируем манифесты и ставим зависимости **игнорируя** расширения,
# которых нет в composer-image. Заодно убираем прогресс-бар — меньше RAM.
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev --prefer-dist --optimize-autoloader \
        --no-interaction --no-scripts --no-progress \
        --ignore-platform-reqs

######################## 2. node-stage (Vite) ############################
FROM node:18 AS node-build
WORKDIR /app
# Ограничиваем память, иначе `npm ci` легко ест >1 ГБ
ENV NODE_OPTIONS=--max_old_space_size=512
COPY package*.json ./
RUN npm ci --omit=dev --no-audit --silent
COPY resources/ resources/
COPY vite.config.* ./
RUN npm run build

######################## 3. runtime-stage ################################
FROM php:8.3-cli

# --- пакеты + расширения, как раньше, но компилируем Imagick в один поток
RUN set -ex \
 && apt-get update \
 && apt-get install -y --no-install-recommends \
      git curl unzip zip ca-certificates \
      build-essential pkg-config libzip-dev libicu-dev libexif-dev \
      libmagickwand-dev imagemagick libpng-dev libjpeg62-turbo-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-configure intl \
 && docker-php-ext-install -j"$(nproc)" pdo_mysql pdo_pgsql mbstring exif intl bcmath gd zip \
 # ------------ Imagick -----------------
 && git clone --depth 1 https://github.com/Imagick/imagick.git /usr/src/imagick \
 && cd /usr/src/imagick \
 && phpize && ./configure && make -j1 && make install \
 && docker-php-ext-enable imagick \
 && rm -rf /var/lib/apt/lists/* /usr/src/imagick

# Composer уже собрал vendor-папку, просто копируем
COPY --from=composer-deps /app/vendor /var/www/html/vendor
# Фронт-сборку тоже
COPY --from=node-build /app/public/build /var/www/html/public/build
# Копируем весь код
WORKDIR /var/www/html
COPY . .

# Кэшируем конфиги/роуты/представления
RUN php artisan config:cache && php artisan route:cache && php artisan view:cache

# Railway даёт случайный порт через $PORT :contentReference[oaicite:1]{index=1}
ENV LOG_CHANNEL=errorlog APP_ENV=production
EXPOSE 8080

CMD php artisan migrate --force && php artisan storage:link \
 && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
