# ==============================================================================
# STAGE 1: PHP Vendor Dependencies (Needed for Filament theme CSS)
# ==============================================================================
FROM composer:2 AS vendor-builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist --ignore-platform-reqs

# ==============================================================================
# STAGE 2: Frontend Asset Compilation (Node & Vite)
# ==============================================================================
FROM node:22-alpine AS frontend-builder

WORKDIR /app

# Copy package manifests & install Node dependencies
COPY package.json package-lock.json ./
RUN npm ci

# Copy Filament vendor CSS from Stage 1 so Vite can resolve @import theme.css
COPY --from=vendor-builder /app/vendor/filament ./vendor/filament

# Copy source assets, public folder, AND application PHP code so Tailwind scans all classes
COPY resources ./resources
COPY public ./public
COPY app ./app
COPY vite.config.js ./
RUN npm run build

# ==============================================================================
# STAGE 3: Production PHP 8.3 FPM + Nginx + Process Supervision
# ==============================================================================
FROM php:8.4-fpm-bookworm

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND=noninteractive

# 1. Install System Dependencies & CLI Tools
RUN apt-get update && apt-get install -y --no-install-recommends \
    nginx \
    supervisor \
    cron \
    git \
    curl \
    unzip \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libzip-dev \
    libicu-dev \
    libxml2-dev \
    libonig-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# 2. Configure & Install Required PHP Extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        bcmath \
        exif \
        gd \
        intl \
        zip \
        pcntl \
        opcache

# 3. Copy Official Composer Binary
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Copy Application Source Code
COPY . /var/www/html

# 5. Copy Precompiled Vite Assets from Stage 2
COPY --from=frontend-builder /app/public/build /var/www/html/public/build

# 6. Install PHP Production Dependencies & Run Autoloader
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-progress

# 7. Backup Baseline Seed Storage (For Hydration on Volume Mount)
RUN mkdir -p /var/www/html/storage_defaults && \
    cp -r /var/www/html/storage/app/* /var/www/html/storage_defaults/

# 8. Configure Nginx, PHP, and Process Management
RUN rm -f /etc/nginx/sites-enabled/default
COPY docker/nginx.conf /etc/nginx/conf.d/default.conf
COPY docker/php.ini $PHP_INI_DIR/conf.d/99-custom.ini
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh


# 10. Network & Lifecycle Entrypoint
EXPOSE 80

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
