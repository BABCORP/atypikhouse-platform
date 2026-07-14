#!/bin/sh
set -eu

if [ "${DB_AUTO_IMPORT:-false}" != "true" ]; then
    exit 0
fi

if [ -z "${DB_HOST:-}" ] || [ -z "${DB_PORT:-}" ] || [ -z "${DB_DATABASE:-}" ] || [ -z "${DB_USERNAME:-}" ] || [ -z "${DB_PASSWORD:-}" ]; then
    echo "DB_AUTO_IMPORT=true mais une variable DB_* manque. Requis: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD." >&2
    exit 1
fi

SCHEMA_FILE="/var/www/html/database/schema.sql"
SEED_FILE="/var/www/html/database/seed.sql"

if [ ! -f "$SCHEMA_FILE" ] || [ ! -f "$SEED_FILE" ]; then
    echo "Fichiers SQL introuvables dans /var/www/html/database." >&2
    exit 1
fi

echo "Vérification de la base MySQL distante..."

TABLES_COUNT="$(
    MYSQL_PWD="$DB_PASSWORD" mysql \
        --protocol=TCP \
        -h "$DB_HOST" \
        -P "$DB_PORT" \
        -u "$DB_USERNAME" \
        "$DB_DATABASE" \
        -N -B \
        -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE();" 2>/dev/null || echo "connection_failed"
)"

if [ "$TABLES_COUNT" = "connection_failed" ]; then
    echo "Connexion MySQL impossible. Vérifiez DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD et DB_DATABASE." >&2
    exit 1
fi

if [ "$TABLES_COUNT" != "0" ] && [ "${DB_AUTO_IMPORT_FORCE:-false}" != "true" ]; then
    echo "Base MySQL déjà initialisée (${TABLES_COUNT} tables). Import ignoré."
    exit 0
fi

echo "Import du schéma AtypikHouse dans ${DB_DATABASE}..."
sed '/^CREATE DATABASE /d;/^USE /d' "$SCHEMA_FILE" | MYSQL_PWD="$DB_PASSWORD" mysql \
    --protocol=TCP \
    -h "$DB_HOST" \
    -P "$DB_PORT" \
    -u "$DB_USERNAME" \
    "$DB_DATABASE"

echo "Import des données de démonstration..."
sed '/^USE /d' "$SEED_FILE" | MYSQL_PWD="$DB_PASSWORD" mysql \
    --protocol=TCP \
    -h "$DB_HOST" \
    -P "$DB_PORT" \
    -u "$DB_USERNAME" \
    "$DB_DATABASE"

echo "Base AtypikHouse initialisée."
