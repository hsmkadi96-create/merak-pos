#!/bin/bash

echo "=== DEMARRAGE MERAK POS ==="

# Wait for database
sleep 20

# Copy .env.build as .env if needed
if [ ! -f .env ]; then
    cp .env.build .env 2>/dev/null || true
fi

# Generate app key if missing
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force 2>/dev/null || true
fi

# Clear caches to avoid conflicts
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Run migrations and seeders
echo "Execution des migrations..."
php artisan migrate --force --seed 2>/dev/null || true

# Cache config
php artisan config:cache 2>/dev/null || true

# Storage link
php artisan storage:link 2>/dev/null || true

# Permissions
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data /var/www 2>/dev/null || true

# Start Apache on port 10000
echo "Apache demarre sur le port 10000"
exec apache2-foreground