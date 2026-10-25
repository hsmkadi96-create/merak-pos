#!/bin/bash

echo "=== DEMARRAGE MERAK POS ==="

# Attendre que la base de données soit prête
echo "Attente de la base de donnees..."
sleep 20

# Copier .env.build vers .env si .env n'existe pas
if [ ! -f .env ]; then
    cp .env.build .env 2>/dev/null || true
fi

# Generer la cle d'application si absente
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force 2>/dev/null || true
fi

# Nettoyer les caches pour eviter les conflits
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true

# Executer les migrations et seeders
echo "Execution des migrations..."
php artisan migrate --force --seed 2>/dev/null || true

# Recacher la configuration
php artisan config:cache 2>/dev/null || true
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

# Creer le lien symbolique pour le stockage
php artisan storage:link 2>/dev/null || true

# Definir les permissions
chmod -R 775 storage bootstrap/cache 2>/dev/null || true
chown -R www-data:www-data /var/www 2>/dev/null || true

# Demarrer Apache en foreground
echo "Demarrage d'Apache sur le port 10000..."
apache2-foreground