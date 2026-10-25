#!/bin/bash

# Wait for database to be ready
sleep 10

# Run migrations and seed
php artisan migrate --force --seed 2>/dev/null || true

# Cache configuration
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Create storage link
php artisan storage:link 2>/dev/null || true

# Start Apache in foreground
apache2-foreground