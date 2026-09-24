#!/usr/bin/env bash
# À exécuter SUR Hostinger (session SSH déjà ouverte) :
#   cd ~/domains/talentsdumaroc.com/laravel
#   bash scripts/export-db-dump.sh
#
# Produit : storage/app/private/hostinger-restore-$(date).sql.gz

set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$APP_DIR"

if [ ! -f .env ]; then
  echo "Fichier .env introuvable dans $APP_DIR" >&2
  exit 1
fi

get_env() {
  local key="$1"
  local line
  line="$(grep -E "^${key}=" .env | tail -n 1 || true)"
  line="${line#${key}=}"
  line="${line%\"}"
  line="${line#\"}"
  line="${line%\'}"
  line="${line#\'}"
  printf '%s' "$line"
}

DB_HOST="$(get_env DB_HOST)"
DB_PORT="$(get_env DB_PORT)"
DB_DATABASE="$(get_env DB_DATABASE)"
DB_USERNAME="$(get_env DB_USERNAME)"
DB_PASSWORD="$(get_env DB_PASSWORD)"

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_PORT="${DB_PORT:-3306}"

if [ -z "$DB_DATABASE" ] || [ -z "$DB_USERNAME" ]; then
  echo "DB_DATABASE / DB_USERNAME manquants dans .env" >&2
  exit 1
fi

OUT_DIR="$APP_DIR/storage/app/private"
mkdir -p "$OUT_DIR"
STAMP="$(date +%Y%m%d-%H%M%S)"
OUT_SQL="$OUT_DIR/hostinger-restore-$STAMP.sql"
OUT_GZ="$OUT_SQL.gz"

echo "→ Export $DB_DATABASE@$DB_HOST:$DB_PORT …"

MYSQL_PWD="$DB_PASSWORD" mysqldump \
  -h "$DB_HOST" \
  -P "$DB_PORT" \
  -u "$DB_USERNAME" \
  --single-transaction \
  --routines \
  --triggers \
  --no-tablespaces \
  --default-character-set=utf8mb4 \
  "$DB_DATABASE" > "$OUT_SQL"

gzip -f "$OUT_SQL"

echo "✓ Dump créé : $OUT_GZ"
echo "  Taille : $(du -h "$OUT_GZ" | awk '{print $1}')"
ls -la "$OUT_GZ"
