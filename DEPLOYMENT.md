# 🚀 Guide de Déploiement — MERAK POS

Déploiement 100% gratuit (0 DA) sans VPS payant.

---

## 📋 Prérequis

- Un ordinateur avec **PHP 8.2+** et **Composer** installés
- Une base de données **MySQL** (ou MariaDB)
- **Node.js** et **NPM** (pour compiler les assets)
- Un compte gratuit sur l'une des plateformes ci-dessous

---

## ☁️ Option 1 : Oracle Cloud Free Tier (Recommandé — le plus puissant)

Oracle propose un **VPS gratuit** avec :
- 4 ARM CPU, 24 Go RAM, 200 Go stockage
- 2 instances AMD (1/8 OCPU, 1 Go RAM chacune)

### Étapes :

1. **Créer un compte** sur [oracle.com/cloud/free](https://www.oracle.com/cloud/free/)
2. **Créer une instance Compute** :
   - Image : Ubuntu 22.04 LTS
   - Shape : VM.Standard.A1.Flex (ARM) — 4 OCPU, 24 Go RAM
   - Ajouter votre clé SSH publique

3. **Se connecter en SSH** :
   ```bash
   ssh ubuntu@VOTRE_IP_PUBLIQUE
   ```

4. **Installer le stack LEMP** :
   ```bash
   sudo apt update && sudo apt upgrade -y
   sudo apt install -y nginx mysql-server php8.2-fpm php8.2-mysql php8.2-xml \
     php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath \
     php8.2-intl php8.2-soap composer unzip git curl
   ```

5. **Configurer MySQL** :
   ```bash
   sudo mysql -u root
   ```
   ```sql
   CREATE DATABASE merak_pos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   CREATE USER 'merak'@'localhost' IDENTIFIED BY 'VOTRE_MOT_DE_PASSE';
   GRANT ALL PRIVILEGES ON merak_pos.* TO 'merak'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

6. **Installer Composer** :
   ```bash
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php composer-setup.php --install-dir=/usr/local/bin --filename=composer
   php -r "unlink('composer-setup.php');"
   ```

7. **Installer Node.js** :
   ```bash
   curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
   sudo apt install -y nodejs
   ```

8. **Déployer l'application** :
   ```bash
   cd /var/www
   sudo git clone https://github.com/VOTRE_COMPTE/merak-pos.git merak-pos
   cd merak-pos
   sudo cp .env.example .env  # Ou créez votre .env manuellement
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```

9. **Configurer Nginx** :
   ```bash
   sudo nano /etc/nginx/sites-available/merak-pos
   ```
   ```nginx
   server {
       listen 80;
       server_name VOTRE_DOMAINE;
       root /var/www/merak-pos/public;
       index index.php;

       location / {
           try_files $uri $uri/ /index.php?$query_string;
       }

       location ~ \.php$ {
           include snippets/fastcgi-php.conf;
           fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
       }

       location ~ /\.ht {
           deny all;
       }
   }
   ```

   ```bash
   sudo ln -s /etc/nginx/sites-available/merak-pos /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl reload nginx
   ```

10. **Configurer le .env** :
    ```
    APP_NAME="MERAK POS"
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://VOTRE_DOMAINE

    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=merak_pos
    DB_USERNAME=merak
    DB_PASSWORD=VOTRE_MOT_DE_PASSE

    CIB_API_KEY=VOTRE_CLE_CIB
    CIB_API_SECRET=VOTRE_SECRET_CIB
    CIB_API_URL=https://api.cib.dz/sandbox

    DAHABIA_API_KEY=VOTRE_CLE_DAHABIA
    DAHABIA_API_SECRET=VOTRE_SECRET_DAHABIA
    DAHABIA_API_URL=https://api.dahabia.dz/sandbox

    CHIF_AA_API_KEY=VOTRE_CLE_CHIF AA
    CHIF_AA_API_SECRET=VOTRE_SECRET_CHIF AA
    CHIF_AA_API_URL=https://api.chifaa.dz/sandbox
    ```

11. **SSL Gratuit (Let's Encrypt)** :
    ```bash
    sudo apt install -y certbot python3-certbot-nginx
    sudo certbot --nginx -d VOTRE_DOMAINE
    ```

12. **Queue Worker** (pour les emails, notifications) :
    ```bash
    sudo nano /etc/systemd/system/merak-worker.service
    ```
    ```ini
    [Unit]
    Description=MERAK POS Queue Worker
    After=network.target

    [Service]
    User=ubuntu
    Group=www-data
    Restart=always
    ExecStart=/usr/bin/php /var/www/merak-pos/artisan queue:work --sleep=3 --tries=3
    WorkingDirectory=/var/www/merak-pos

    [Install]
    WantedBy=multi-user.target
    ```
    ```bash
    sudo systemctl enable merak-worker
    sudo systemctl start merak-worker
    ```

---

## 🌐 Option 2 : Render.com (PaaS Gratuit)

Render propose un hébergement gratuit pour applications web.

### Étapes :

1. Pousser votre code sur **GitHub**
2. Se connecter sur [render.com](https://render.com)
3. Créer un **Web Service** → Connecter votre repo GitHub
4. Configuration :
   - **Runtime** : Docker (ou utiliser un Dockerfile personnalisé)
   - **Build Command** : `composer install && npm install && npm run build && php artisan key:generate && php artisan migrate --seed`
   - **Start Command** : `php artisan serve --host=0.0.0.0 --port=$PORT`
5. Ajouter une base de données **PostgreSQL** ou **MySQL** gratuite sur Render
6. Configurer les variables d'environnement dans le dashboard Render

**Limite gratuite** : Le service se met en veille après 15 min d'inactivité.

---

## 🚄 Option 3 : Railway.app (Essai Gratuit)

Railway offre un crédit gratuit pour commencer.

### Étapes :

1. Se connecter sur [railway.app](https://railway.app) avec GitHub
2. Créer un **New Project** → Déployer depuis GitHub
3. Ajouter une base de données MySQL
4. Configurer les variables d'environnement
5. Déployer automatiquement

---

## 📱 Configuration du Sous-domaine (SAAS)

Pour le module SAAS multi-tenant, chaque client peut avoir son propre sous-domaine :

```
client1.merakpos.dz
client2.merakpos.dz
```

### Configuration DNS :

Ajoutez un enregistrement DNS wildcard :
```
Type: A ou CNAME
Nom: *.merakpos.dz
Valeur: VOTRE_IP_OU_DOMAINE
TTL: Automatique
```

### Configuration Laravel :

Dans `config/app.php`, ajoutez :
```php
'tenant_domains' => [
    'client1.merakpos.dz' => 1,  // ID du tenant
    'client2.merakpos.dz' => 2,
],
```

---

## 🔑 Clés API Paiement (Champs configurables)

Les clés CIB, DAHABIA et Chifaa sont configurables dans `.env` :

| Variable | Description | Valeur par défaut |
|----------|-------------|-------------------|
| `CIB_API_KEY` | Clé API CIB | (vide — mode sandbox) |
| `CIB_API_SECRET` | Secret API CIB | (vide) |
| `CIB_API_URL` | URL API CIB | `https://api.cib.dz/sandbox` |
| `DAHABIA_API_KEY` | Clé API DAHABIA | (vide) |
| `DAHABIA_API_SECRET` | Secret API DAHABIA | (vide) |
| `DAHABIA_API_URL` | URL API DAHABIA | `https://api.dahabia.dz/sandbox` |
| `CHIF_AA_API_KEY` | Clé API Chifaa | (vide) |
| `CHIF_AA_API_SECRET` | Secret API Chifaa | (vide) |
| `CHIF_AA_API_URL` | URL API Chifaa | `https://api.chifaa.dz/sandbox` |

> ⚠️ En mode sandbox, les paiements sont simulés. Contactez les banques pour obtenir les clés de production.

---

## 🔐 Sécurité Post-Déploiement

```bash
# 1. Générer une nouvelle clé d'application
php artisan key:generate

# 2. Cacher la configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Définir les permissions
sudo chown -R www-data:www-data /var/www/merak-pos/storage
sudo chown -R www-data:www-data /var/www/merak-pos/bootstrap/cache
sudo chmod -R 775 /var/www/merak-pos/storage
sudo chmod -R 775 /var/www/merak-pos/bootstrap/cache

# 4. Désactiver le debug en production
# Dans .env : APP_DEBUG=false
```

---

## 📊 Accès à l'application

| Module | URL | Description |
|--------|-----|-------------|
| Dashboard | `/` | Tableau de bord principal |
| SAAS | `/saas/dashboard` | Gestion multi-tenant |
| Zakat | `/zakat` | Rapport Zakat |
| Restaurant | `/restaurant` | Gestion tables & commandes |
| Clinique | `/clinic` | Gestion patients & RDV |
| Face ID | `/faceid` | Pointage reconnaissance faciale |
| Sécurité | `/security/audit` | Audit des failles |
| Paiements | `/api/payment/*` | CIB, DAHABIA, Chifaa |

---

## 👨‍💻 Développé par

**H-KADI** — Champ non modifiable

---

## 📞 Support

Pour toute question sur le déploiement, consultez la documentation dans `documentation/` ou ouvrez une issue sur GitHub.