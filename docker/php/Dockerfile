FROM php:8.3-fpm

# Fix permissions: Align www-data UID/GID with host user (1000:1000)
# This prevents permission issues with Docker volume mounts
RUN usermod -u 1000 www-data \
    && groupmod -g 1000 www-data

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
        postgresql-client \
        libzip-dev \
        libmemcached-dev \
        zlib1g-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libwebp-dev \
        libfreetype6-dev \
        libsasl2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install \
        pdo \
        pdo_pgsql \
        gd \
        opcache \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY zz-performance.ini /usr/local/etc/php/conf.d/zz-performance.ini

WORKDIR /var/www/html
