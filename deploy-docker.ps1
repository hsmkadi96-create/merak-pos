# ============================================================================
# SCRIPT DE DÉPLOIEMENT DOCKER — MERAK POS SUR RENDER
# Fichier : deploy-docker.ps1
# ============================================================================

Write-Host "=== DÉPLOIEMENT DOCKER MERAK POS ===" -ForegroundColor Cyan
Write-Host ""

# 1. Configuration Git
Write-Host "Configuration Git..." -ForegroundColor Yellow
git config --global user.email "hsmkadi96@gmail.com"
git config --global user.name "H-KADI"

# 2. Ajout des fichiers Docker
Write-Host "Ajout des fichiers..." -ForegroundColor Yellow
git add .

# 3. Commit
Write-Host "Création du commit..." -ForegroundColor Yellow
git commit -m "Ajout Dockerfile pour déploiement Render"

# 4. Push
Write-Host "Push vers GitHub..." -ForegroundColor Yellow
git push -u origin main

Write-Host ""
Write-Host "=== TERMINÉ ! ===" -ForegroundColor Green
Write-Host "Votre code est sur GitHub : https://github.com/hsmkadi96-create/merak-pos" -ForegroundColor Green
Write-Host ""
Write-Host "MAINTENANT, allez sur Render.com :"
Write-Host "1. New + → Web Service"
Write-Host "2. Connectez GitHub, sélectionnez merak-pos"
Write-Host "3. Choisissez Runtime: Docker"
Write-Host "4. Ajoutez les variables d'environnement (voir ci-dessous)"
Write-Host "5. Cliquez sur Create Web Service"
Write-Host ""
Write-Host "Variables d'environnement à ajouter :"
Write-Host "  APP_NAME = MERAK POS"
Write-Host "  APP_ENV = production"
Write-Host "  APP_DEBUG = false"
Write-Host "  DB_CONNECTION = pgsql"
Write-Host "  DB_HOST = dpg-dbf419dg1s2s73ed76e0-a"
Write-Host "  DB_PORT = 5432"
Write-Host "  DB_DATABASE = merak_pos"
Write-Host "  DB_USERNAME = merak_user"
Write-Host "  DB_PASSWORD = J4VqugUNKkHR4BWZsq5qdN93YittNT8y"
Write-Host ""
Write-Host "Appuyez sur une touche pour fermer..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")