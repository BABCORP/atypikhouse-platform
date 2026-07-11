#!/bin/sh
set -eu

export APACHE_PORT="${PORT:-10000}"

sed -i "s/Listen 80/Listen ${APACHE_PORT}/" /etc/apache2/ports.conf
sed -i "s/\${APACHE_PORT}/${APACHE_PORT}/g" /etc/apache2/sites-available/000-default.conf

exec "$@"
