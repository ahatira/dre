
FROM php:8.3-fpm

# Install system dependencies, PHP extensions, and Node.js LTS (18.x) required by Drupal
RUN apt-get update \
  && apt-get install -y --no-install-recommends \
    git curl unzip libpng-dev libjpeg-dev libfreetype6-dev libxml2-dev \
    libzip-dev zlib1g-dev libicu-dev libonig-dev libpq-dev \
    libwebp-dev libavif-dev ca-certificates \
  && curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
  && apt-get install -y nodejs \
  && ln -sf /usr/bin/npm /usr/local/bin/npm \
  && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp --with-avif \
  && docker-php-ext-install -j$(nproc) \
    pdo pdo_pgsql pgsql gd xml zip intl opcache bcmath

# Optional: install APCu
RUN pecl install apcu && docker-php-ext-enable apcu || true

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Create www-data directory and ensure permissions
RUN usermod -u 1000 www-data || true
COPY php.ini /usr/local/etc/php/conf.d/zz-custom.ini

WORKDIR /var/www/html

# Correction ownership récursif pour www-data
RUN chown -R www-data:www-data /var/www/html \
&& ls
# Port pour PHP-FPM
EXPOSE 9000

# Copie le script d'entrypoint
COPY entrypoint-perms.sh /usr/local/bin/entrypoint-perms.sh
RUN chmod +x /usr/local/bin/entrypoint-perms.sh

USER 1000

ENTRYPOINT ["/usr/local/bin/entrypoint-perms.sh"]
CMD ["php-fpm"]
