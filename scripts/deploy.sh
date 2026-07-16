#!/usr/bin/env bash
# Déploiement production — Hostinger mutualisé (SSH) ou manuellement.
# Usage : bash scripts/deploy.sh
#
# Assets front : builder en local (npm run build) et committer public/build/
# avant le push — pas de npm sur le serveur mutualisé.
#
# Binaires (optionnel) :
#   PHP_BIN=/usr/bin/php83 COMPOSER_BIN=$HOME/bin/composer bash scripts/deploy.sh

set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$APP_DIR"

resolve_php() {
    if [ -n "${PHP_BIN:-}" ] && command -v "$PHP_BIN" >/dev/null 2>&1; then
        echo "$PHP_BIN"
        return
    fi
    if command -v php >/dev/null 2>&1; then
        command -v php
        return
    fi
    for candidate in php83 php8.3 php82 php8.2 /usr/bin/php83 /usr/bin/php8.3 /opt/alt/php83/usr/bin/php; do
        if command -v "$candidate" >/dev/null 2>&1 || [ -x "$candidate" ]; then
            echo "$candidate"
            return
        fi
    done
    echo "ERREUR : PHP introuvable. Définissez PHP_BIN=/chemin/vers/php" >&2
    exit 1
}

resolve_composer() {
    if [ -n "${COMPOSER_BIN:-}" ] && command -v "$COMPOSER_BIN" >/dev/null 2>&1; then
        echo "$COMPOSER_BIN"
        return
    fi
    if command -v composer >/dev/null 2>&1; then
        command -v composer
        return
    fi
    for candidate in "$HOME/bin/composer" /usr/local/bin/composer /usr/bin/composer; do
        if [ -x "$candidate" ]; then
            echo "$candidate"
            return
        fi
    done
    echo "ERREUR : Composer introuvable. Définissez COMPOSER_BIN=/chemin/vers/composer" >&2
    exit 1
}

PHP_BIN="$(resolve_php)"
COMPOSER_BIN="$(resolve_composer)"

echo "→ Déploiement dans $APP_DIR"
echo "→ PHP=$PHP_BIN | Composer=$COMPOSER_BIN"

if [ ! -f .env ]; then
    echo "ERREUR : fichier .env manquant. Copiez .env.example vers .env et configurez-le."
    exit 1
fi

echo "→ git pull"
git pull --ff-only origin main

# Recharger le script après pull (sinon seed/migrate ajoutés ne s'exécutent pas).
if [ "${DEPLOY_REEXEC:-}" != "1" ]; then
    export DEPLOY_REEXEC=1
    exec bash "$0" "$@"
fi

echo "→ composer install (--no-dev)"
$COMPOSER_BIN install --no-interaction --prefer-dist --optimize-autoloader --no-dev

if [ ! -L public/storage ] && [ ! -e public/storage ]; then
    echo "→ storage:link"
    $PHP_BIN artisan storage:link
elif [ ! -L public/storage ]; then
    echo "→ storage:link (remplacement de public/storage)"
    rm -rf public/storage
    $PHP_BIN artisan storage:link
fi

echo "→ migrations"
$PHP_BIN artisan migrate --force

echo "→ seed (référence prod : services, métiers, social, admin)"
$PHP_BIN artisan db:seed --force --class=ProductionDataSeeder

echo "→ cache"
$PHP_BIN artisan config:cache
$PHP_BIN artisan route:cache
$PHP_BIN artisan view:cache

echo "→ permissions"
chmod -R ug+rwx storage bootstrap/cache 2>/dev/null || true

echo "✓ Déploiement terminé — $(date)"
