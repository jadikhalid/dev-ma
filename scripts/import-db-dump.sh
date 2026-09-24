#!/usr/bin/env bash
# Import local d'un dump Hostinger dans la BD .env (Windows Git Bash / WSL / Linux).
# Usage :
#   bash scripts/import-db-dump.sh chemin/vers/hostinger-restore-XXXX.sql.gz
#   bash scripts/import-db-dump.sh chemin/vers/hostinger-restore-XXXX.sql

set -euo pipefail

APP_DIR="$(cd "$(dirname "$0")/.." && pwd)"
cd "$APP_DIR"

DUMP_PATH="${1:-}"
if [ -z "$DUMP_PATH" ] || [ ! -f "$DUMP_PATH" ]; then
  echo "Usage: bash scripts/import-db-dump.sh <dump.sql|dump.sql.gz>" >&2
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

echo "⚠ Ceci VA ÉCRASER la base locale : $DB_DATABASE@$DB_HOST"
echo "   Source : $DUMP_PATH"
read -r -p "Confirmer (oui) : " confirm
if [ "$confirm" != "oui" ]; then
  echo "Annulé."
  exit 1
fi

TMP_SQL="$DUMP_PATH"
CLEANUP=""
case "$DUMP_PATH" in
  *.gz)
    TMP_SQL="$(mktemp).sql"
    CLEANUP="$TMP_SQL"
    gunzip -c "$DUMP_PATH" > "$TMP_SQL"
    ;;
esac

echo "→ Drop + recreate schema…"
MYSQL_PWD="$DB_PASSWORD" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -e "DROP DATABASE IF EXISTS \`$DB_DATABASE\`; CREATE DATABASE \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "→ Import…"
MYSQL_PWD="$DB_PASSWORD" mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" "$DB_DATABASE" < "$TMP_SQL"

if [ -n "$CLEANUP" ]; then
  rm -f "$CLEANUP"
fi

echo "→ php artisan migrate --force (schéma local éventuellement plus récent)…"
php artisan migrate --force --no-interaction

echo "→ Counts…"
php artisan tinker --execute="echo 'users='.App\\Models\\User::count().PHP_EOL; echo 'profiles='.App\\Models\\Profile::count().PHP_EOL; echo 'feed='.App\\Models\\SocialFeedItem::count().PHP_EOL;"

echo "✓ Restauration terminée."
