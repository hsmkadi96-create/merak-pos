FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpng-dev libonig-dev libxml2-dev \
    libzip-dev libsqlite3-dev libpq-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www

# Copy project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key
RUN php artisan key:generate --force 2>/dev/null || true

# Expose port 10000 (Render default)
EXPOSE 10000

# Start script
COPY start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]