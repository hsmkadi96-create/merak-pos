# ============================================================================
# SCRIPT DE DÉPLOIEMENT FINAL — MERAK POS SUR RENDER (Docker)
# ============================================================================

Write-Host "=== DEPLOIEMENT MERAK POS ===" -ForegroundColor Cyan

# 1. Aller dans le projet
cd "C:\Users\mRm-Hani\Desktop\codecanyon-31445124-stockyultimate-inventory-management-system-with-pos"

# 2. Créer .env pour le build Docker
Copy-Item ".env.build" ".env" -Force

# 3. Configuration Git
git config --global user.email "hsmkadi96@gmail.com"
git config --global user.name "H-KADI"

# 4. Initialiser Git si necessaire
if (-not (Test-Path ".git")) {
    git init
}

# 5. Ajouter tous les fichiers
git add .

# 6. Commit
git commit -m "MERAK POS - Docker configure pour Render"

# 7. Lier GitHub
git remote remove origin 2>$null
git remote add origin git@github.com:hsmkadi96-create/merak-pos.git
git branch -M main

# 8. Push vers GitHub
Write-Host "Push vers GitHub..." -ForegroundColor Yellow
git push -u origin main -f

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host " CODE PUSHE SUR GITHUB ! " -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "ALLEZ SUR RENDER.COM :"
Write-Host "1. New + -> Web Service"
Write-Host "2. Connectez GitHub, selectionnez merak-pos"
Write-Host "3. Runtime : DOCKER"
Write-Host "4. Name : merak-pos"
Write-Host "5. Cliquez sur Create Web Service"
Write-Host ""
Write-Host "Variables d'environnement :"
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
Write-Host "Votre app sera sur : https://merak-pos.onrender.com"
Write-Host ""
Write-Host "Appuyez sur une touche pour fermer..."
pause