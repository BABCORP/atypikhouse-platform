#!/bin/sh
set -eu

export APACHE_PORT="${PORT:-10000}"

if grep -q "Listen 80" /etc/apache2/ports.conf; then
    sed -i "s/Listen 80/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
elif ! grep -q "Listen ${APACHE_PORT}" /etc/apache2/ports.conf; then
    echo "Listen ${APACHE_PORT}" >> /etc/apache2/ports.conf
fi

sed -i "s/\${APACHE_PORT}/${APACHE_PORT}/g" /etc/apache2/sites-available/000-default.conf

mkdir -p /var/www/html/storage/uploads /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage || true

if [ ! -f /var/www/html/public/index.php ]; then
    echo "AtypikHouse deploy error: public/index.php introuvable. Vérifiez le Root Directory Render et le Dockerfile Path." >&2
    exit 1
fi

echo "AtypikHouse starting on port ${APACHE_PORT}"

exec "$@"
