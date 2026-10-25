FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libpq-dev libicu-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip intl \
    && docker-php-ext-configure intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Copy .env.build as .env for Laravel
RUN cp .env.build .env 2>/dev/null || true

# Allow composer to run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader 2>&1 || true

# Generate application key
RUN php artisan key:generate --force 2>/dev/null || true

# Set permissions for storage and cache
RUN chmod -R 775 storage bootstrap/cache 2>/dev/null || true
RUN chown -R www-data:www-data /var/www 2>/dev/null || true

# Expose port 10000 (Render default)
EXPOSE 10000

# Start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]