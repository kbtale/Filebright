# Stage 1: Build dependencies
FROM php:8.4-fpm as build

# Install system dependencies
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  libpq-dev \
  libssl-dev \
  zip \
  unzip \
  && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd \
  && pecl install mongodb \
  && docker-php-ext-enable mongodb

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

# Install dependencies and optimize
RUN composer install --no-dev --optimize-autoloader

FROM php:8.4-fpm-alpine

# Install persistent dependencies
RUN apk add --no-cache \
  libpq \
  libpng \
  libzip \
  oniguruma \
  icu-libs \
  libssl3 \
  postgresql-dev \
  libmagic

# Install PHP extensions in alpine
RUN apk add --no-cache $PHPIZE_DEPS \
  && pecl install mongodb \
  && docker-php-ext-install pdo pdo_pgsql \
  && docker-php-ext-enable mongodb \
  && apk del $PHPIZE_DEPS \
  && apk del postgresql-dev \
  && apk add --no-cache libpq

WORKDIR /var/www
COPY --from=build /var/www .

# Copy and setup entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Adjust permissions for initial build
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

ENTRYPOINT ["docker-entrypoint.sh"]
EXPOSE 9000
CMD ["php-fpm"]
