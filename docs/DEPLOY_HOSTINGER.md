# Déploiement Hostinger (mutualisé) + 2 PC

Push sur `main` → GitHub Actions (SSH) → `scripts/deploy.sh` sur Hostinger.

```text
PC Windows / Linux Mint  →  git push origin main  →  GitHub Actions  →  Hostinger
```

## Prérequis Hostinger (une fois)

1. Plan avec **SSH** activé (souvent Business+).
2. PHP **8.3+** (hPanel → Advanced → PHP Configuration).
3. Base **MySQL** créée (noter host, database, user, password).
4. Accès SSH : hPanel → Advanced → SSH Access (noter host, port — souvent `65002`, username).

### Document root Laravel

Le domaine doit servir le dossier **`public/`** de l’application, pas la racine du repo.

Options courantes :

- hPanel → Domains → document root = `…/chemin-vers-app/public`
- ou : app dans `~/domains/ton-domaine.com/laravel` et `public_html` en symlink vers `laravel/public`

Ne pas utiliser l’outil Git hPanel qui copie tout dans `public_html` (inadapté à Laravel).

---

## Première installation sur le serveur

En SSH :

```bash
# Exemple de chemin — adapte uXXXX et le domaine
cd ~/domains/ton-domaine.com
git clone https://github.com/jadikhalid/dev-ma.git laravel
cd laravel

cp .env.example .env
nano .env   # ou éditeur hPanel — voir section .env prod ci-dessous

# Composer / PHP : chemins selon Hostinger
composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev
# si besoin : PHP_BIN=php83 COMPOSER_BIN=$HOME/bin/composer …

php artisan key:generate
php artisan storage:link
php artisan migrate --force
php artisan db:seed --force --class=ProductionDataSeeder
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Vérifier : ouvrir le site, puis une URL du type `/storage/avatars/…` si une photo existe.

Pour les déploiements suivants, le script fait déjà pull + composer + migrate + seed + cache :

```bash
bash scripts/deploy.sh
```

---

## Secrets GitHub Actions

Repo GitHub → **Settings → Secrets and variables → Actions** → créer :

| Secret | Exemple / note |
|--------|----------------|
| `HOSTINGER_HOST` | IP ou hostname SSH (hPanel) |
| `HOSTINGER_USERNAME` | User SSH |
| `HOSTINGER_SSH_KEY` | Clé **privée** complète (`-----BEGIN … PRIVATE KEY-----`) |
| `HOSTINGER_PORT` | Souvent `65002` |
| `HOSTINGER_PATH` | Chemin absolu de l’app, ex. `/home/uXXXX/domains/ton-domaine.com/laravel` |

### Clé SSH dédiée déploiement

Sur ton PC (une fois) :

```bash
ssh-keygen -t ed25519 -C "github-deploy-hostinger" -f hostinger_deploy -N ""
```

- Contenu de `hostinger_deploy.pub` → hPanel → SSH Access (authorized keys)
- Contenu de `hostinger_deploy` (privé) → secret `HOSTINGER_SSH_KEY`
- Ne jamais committer la clé privée

Test manuel :

```bash
ssh -i hostinger_deploy -p 65002 USER@HOST
```

Après le premier push du workflow (`.github/workflows/deploy-hostinger.yml`), chaque push sur `main` déclenche le déploiement. Tu peux aussi lancer **Actions → Deploy Hostinger → Run workflow**.

---

## Workflow quotidien (Windows ou Linux Mint)

```bash
git pull origin main

# … développement …

# Si tu as modifié CSS/JS/Blade assets Vite :
npm run build
# → committer aussi public/build/ (pas de npm sur le mutualisé)

git add -A
git commit -m "…"
git push origin main
# → GitHub Actions déploie automatiquement
```

### Règles bi-PC

- Toujours `git pull` avant de commencer à coder
- Une seule branche de production : `main`
- `.env` local (Mailpit, SQLite/MySQL local) **≠** `.env` Hostinger (jamais commit)
- Éviter de travailler longtemps hors sync sur les deux PC sans push/pull

---

## Variables `.env` production (minimales)

```env
APP_NAME="Talents du Maroc"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ton-domaine.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=…
DB_USERNAME=…
DB_PASSWORD=…

SESSION_DRIVER=database
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
CACHE_STORE=database

MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=…
MAIL_PASSWORD=…
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=…   # même boîte que MAIL_USERNAME chez Hostinger
MAIL_FROM_NAME="${APP_NAME}"

ADMIN_EMAIL=admin@talentsdumaroc.com
ADMIN_PASSWORD=…      # utilisé par ProductionDataSeeder (updateOrCreate)
```

Local : Mailpit (`MAIL_HOST=127.0.0.1`, `MAIL_PORT=1025`) — voir `.env.example`.

---

## Avant le premier auto-deploy

Committer et pousser sur `main` tous les changements locaux non poussés (migration cleanup, seeders Reference/Demo, fix URLs avatar, workflow, docs). Sinon le serveur n’aura pas ces correctifs au premier `deploy.sh`.

```bash
git status
git add -A
git commit -m "Deploy Hostinger auto + seeders/migration/avatar fixes"
git push origin main
```

---

## Dépannage

| Symptôme | Piste |
|----------|--------|
| Actions : Permission denied (SSH) | Clé publique absente hPanel / mauvais port / mauvais user |
| `PHP introuvable` | Définir `PHP_BIN` dans le shell serveur ou dans `deploy.sh` via env |
| CSS/JS cassés | Relancer `npm run build` en local, committer `public/build/`, push |
| Photos 404 | `php artisan storage:link` + document root = `public` |
| Mails | SMTP Hostinger réel dans `.env` prod, pas Mailpit |
| Seed démo en prod | Utiliser uniquement `ProductionDataSeeder` (déjà le cas dans `deploy.sh`) |
