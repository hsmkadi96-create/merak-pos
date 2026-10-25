#!/bin/bash

echo "=== DEMARRAGE MERAK POS ==="

# Wait for database to be ready
echo "Attente de la base de donnees..."
sleep 15

# Copy .env.build as .env if .env doesn't exist
if [ ! -f .env ]; then
    cp .env.build .env 2>/dev/null || true
fi

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force 2>/dev/null || true
fi

# Run migrations
echo "Execution des migrations..."
php artisan migrate --force --seed 2>/dev/null || true

# Cache configuration
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Create storage link
php artisan storage:link 2>/dev/null || true

# Set permissions
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data /var/www 2>/dev/null || true

# Start Apache in foreground
echo "Demarrage d'Apache sur le port 10000..."
apache2-foreground