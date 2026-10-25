# ============================================================================
# SCRIPT DE DÉPLOIEMENT GIT AUTOMATISÉ — MERAK POS
# Fichier : deploy-git.ps1
# À exécuter depuis PowerShell dans le dossier du projet
# ============================================================================

Write-Host "=== DÉPLOIEMENT GIT MERAK POS ===" -ForegroundColor Cyan
Write-Host ""

# 1. Configuration de l'identité Git (obligatoire)
Write-Host "Configuration de l'identité Git..." -ForegroundColor Yellow
git config --global user.email "hsmkadi96@gmail.com"
git config --global user.name "H-KADI"

# 2. Initialisation du dépôt Git
Write-Host "Initialisation du dépôt Git..." -ForegroundColor Yellow
git init

# 3. Ajout de tous les fichiers
Write-Host "Ajout de tous les fichiers..." -ForegroundColor Yellow
git add .

# 4. Commit
Write-Host "Création du commit..." -ForegroundColor Yellow
git commit -m "MERAK POS - Version complète prête au déploiement"

# 5. Ajout du dépôt distant
Write-Host "Ajout du dépôt distant GitHub..." -ForegroundColor Yellow
git remote remove origin 2>$null
git remote add origin git@github.com:hsmkadi96-create/merak-pos.git

# 6. Renommage de la branche
Write-Host "Renommage de la branche en 'main'..." -ForegroundColor Yellow
git branch -M main

# 7. Push vers GitHub
Write-Host "PUSH vers GitHub (cela peut prendre quelques minutes)..." -ForegroundColor Yellow
git push -u origin main

Write-Host ""
Write-Host "=== TERMINÉ ! ===" -ForegroundColor Green
Write-Host "Votre code est maintenant sur GitHub : https://github.com/hsmkadi96-create/merak-pos" -ForegroundColor Green
Write-Host ""
Write-Host "Appuyez sur une touche pour fermer..."
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")