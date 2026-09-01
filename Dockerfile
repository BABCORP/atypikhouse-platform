FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends default-mysql-client \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite headers \
    && echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf \
    && a2enconf servername

WORKDIR /var/www/html

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY docker/render-entrypoint.sh /usr/local/bin/render-entrypoint
COPY docker/import-database.sh /usr/local/bin/import-database
RUN chmod +x /usr/local/bin/render-entrypoint /usr/local/bin/import-database

COPY . /var/www/html

RUN mkdir -p /var/www/html/storage/uploads /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/storage

EXPOSE 10000

ENTRYPOINT ["render-entrypoint"]
CMD ["apache2-foreground"]
