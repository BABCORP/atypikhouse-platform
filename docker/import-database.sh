#!/bin/sh
set -eu

if [ "${DB_AUTO_IMPORT:-false}" != "true" ]; then
    exit 0
fi

if [ "${APP_ENV:-local}" = "production" ] && [ "${DB_AUTO_IMPORT_FORCE:-false}" = "true" ] && [ "${DB_ALLOW_PRODUCTION_FORCE_IMPORT:-false}" != "true" ]; then
    echo "Import forcé ignoré en production pour éviter de supprimer les comptes créés par les utilisateurs." >&2
    echo "Le service continue de démarrer. Pour une mise à jour en production, utilisez database/render-update.sql." >&2
    exit 0
fi

DATABASE_CONNECTION_URL="${DATABASE_URL:-${MYSQL_URL:-${MYSQL_PUBLIC_URL:-}}}"

if [ -n "$DATABASE_CONNECTION_URL" ]; then
    eval "$(DATABASE_CONNECTION_URL="$DATABASE_CONNECTION_URL" php -r '
        $url = getenv("DATABASE_CONNECTION_URL") ?: "";
        $parsed = parse_url($url);
        if (!is_array($parsed)) {
            exit;
        }
        $map = [
            "DB_HOST" => $parsed["host"] ?? null,
            "DB_PORT" => isset($parsed["port"]) ? (string) $parsed["port"] : null,
            "DB_USERNAME" => isset($parsed["user"]) ? urldecode((string) $parsed["user"]) : null,
            "DB_PASSWORD" => isset($parsed["pass"]) ? urldecode((string) $parsed["pass"]) : null,
            "DB_DATABASE" => isset($parsed["path"]) ? ltrim((string) $parsed["path"], "/") : null,
        ];
        foreach ($map as $key => $value) {
            $current = getenv($key);
            if ($value !== null && $value !== "" && ($current === false || $current === "")) {
                echo "export " . $key . "=" . escapeshellarg($value) . ";\n";
            }
        }
    ')"
fi

export DB_HOST="${DB_HOST:-${MYSQLHOST:-}}"
export DB_PORT="${DB_PORT:-${MYSQLPORT:-3306}}"
export DB_DATABASE="${DB_DATABASE:-${MYSQLDATABASE:-}}"
export DB_USERNAME="${DB_USERNAME:-${MYSQLUSER:-}}"
export DB_PASSWORD="${DB_PASSWORD:-${MYSQLPASSWORD:-}}"

if [ -z "${DB_HOST:-}" ] || [ -z "${DB_PORT:-}" ] || [ -z "${DB_DATABASE:-}" ] || [ -z "${DB_USERNAME:-}" ] || [ -z "${DB_PASSWORD:-}" ]; then
    echo "DB_AUTO_IMPORT=true mais une variable DB_* manque. Requis: DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD." >&2
    echo "Variables acceptées aussi: DATABASE_URL, MYSQL_URL, MYSQL_PUBLIC_URL, MYSQLHOST, MYSQLPORT, MYSQLDATABASE, MYSQLUSER, MYSQLPASSWORD." >&2
    exit 1
fi

case "$DB_DATABASE" in
    *[!a-zA-Z0-9_]*|'')
        echo "DB_DATABASE contient des caractères non autorisés pour l'import automatique." >&2
        exit 1
        ;;
esac

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
        --default-character-set=utf8mb4 \
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
    echo "Base MySQL déjà initialisée (${TABLES_COUNT} tables). Import destructif ignoré pour conserver les comptes utilisateurs."
    exit 0
fi

echo "Configuration UTF-8 de la base ${DB_DATABASE}..."
MYSQL_PWD="$DB_PASSWORD" mysql \
    --protocol=TCP \
    --default-character-set=utf8mb4 \
    --init-command="SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" \
    -h "$DB_HOST" \
    -P "$DB_PORT" \
    -u "$DB_USERNAME" \
    "$DB_DATABASE" \
    -e "ALTER DATABASE \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

echo "Import du schéma AtypikHouse dans ${DB_DATABASE}..."
(printf 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;\n'; sed '/^CREATE DATABASE /d;/^USE /d' "$SCHEMA_FILE") | MYSQL_PWD="$DB_PASSWORD" mysql \
    --protocol=TCP \
    --default-character-set=utf8mb4 \
    --init-command="SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" \
    -h "$DB_HOST" \
    -P "$DB_PORT" \
    -u "$DB_USERNAME" \
    "$DB_DATABASE"

echo "Import des données de démonstration..."
(printf 'SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci;\n'; sed '/^USE /d' "$SEED_FILE") | MYSQL_PWD="$DB_PASSWORD" mysql \
    --protocol=TCP \
    --default-character-set=utf8mb4 \
    --init-command="SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci" \
    -h "$DB_HOST" \
    -P "$DB_PORT" \
    -u "$DB_USERNAME" \
    "$DB_DATABASE"

echo "Base AtypikHouse initialisée."
