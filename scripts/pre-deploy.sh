#!/usr/bin/env bash
# Préparation locale avant push vers GitHub.
# Usage : bash scripts/pre-deploy.sh

set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$APP_DIR"

echo "→ composer install (vérifie vendor local)"
composer install --no-interaction

echo "→ build assets Vite/Tailwind"
npm ci
npm run build

echo "→ tests rapides (optionnel, décommentez si besoin)"
# php artisan test

echo ""
echo "✓ Prêt à committer et pousser."
echo "  git add -A && git commit -m \"...\" && git push origin main"
echo "  Puis sur le serveur : bash scripts/deploy.sh"
