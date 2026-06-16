#!/usr/bin/env bash
# Déploiement production — à exécuter sur le serveur Hostinger via SSH.
# Usage : bash scripts/deploy.sh

set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$APP_DIR"

# Hostinger : si "php" ou "composer" ne marchent pas, essayez :
#   PHP_BIN=/usr/bin/php83 COMPOSER_BIN=/usr/local/bin/composer bash scripts/deploy.sh
PHP_BIN="${PHP_BIN:-php}"
COMPOSER_BIN="${COMPOSER_BIN:-composer}"

echo "→ Déploiement dans $APP_DIR"

if [ ! -f .env ]; then
    echo "ERREUR : fichier .env manquant. Copiez .env.example vers .env et configurez-le."
    exit 1
fi

echo "→ git pull"
git pull --ff-only origin main

echo "→ composer install"
$COMPOSER_BIN install --no-interaction --prefer-dist --optimize-autoloader --no-dev

if [ ! -L public/storage ]; then
    echo "→ storage:link"
    $PHP_BIN artisan storage:link
fi

echo "→ migrations"
$PHP_BIN artisan migrate --force

echo "→ cache"
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

echo "→ permissions"
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "✓ Déploiement terminé — $(date)"
