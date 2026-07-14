# Smoke test local AtypikHouse

Ces commandes servent à vérifier rapidement une installation locale. Elles ne remplacent pas les tests fonctionnels manuels.

## Lint PHP

```bash
find app config public routes -name '*.php' -exec /Applications/MAMP/bin/php/php8.3.30/bin/php -l '{}' \;
```

## JavaScript

```bash
node --check public/assets/js/main.js
```

## Import base de données

Attention : ces commandes réinitialisent la base `atypikhouse`.

```bash
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -proot --socket=/Applications/MAMP/tmp/mysql/mysql.sock -e "DROP DATABASE IF EXISTS atypikhouse; CREATE DATABASE atypikhouse CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -proot --socket=/Applications/MAMP/tmp/mysql/mysql.sock atypikhouse < database/schema.sql
/Applications/MAMP/Library/bin/mysql80/bin/mysql -u root -proot --socket=/Applications/MAMP/tmp/mysql/mysql.sock atypikhouse < database/seed.sql
```

## Serveur local

```bash
DB_HOST=127.0.0.1 DB_PORT=8889 DB_DATABASE=atypikhouse DB_USERNAME=root DB_PASSWORD=root DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock APP_URL=http://127.0.0.1:8010 /Applications/MAMP/bin/php/php8.3.30/bin/php -S 127.0.0.1:8010 -t public public/index.php
```

## Pages publiques à vérifier

```bash
curl -I http://127.0.0.1:8010/
curl -I http://127.0.0.1:8010/hebergements
curl -I http://127.0.0.1:8010/connexion
curl -I http://127.0.0.1:8010/mes-donnees
curl -I http://127.0.0.1:8010/contact
curl -I http://127.0.0.1:8010/mentions-legales
```

## Comptes de démonstration

- Admin : `admin@atypikhouse.fr` / `Admin123!`
- Propriétaire : `proprietaire@atypikhouse.fr` / `Owner123!`
- Locataire : `locataire@atypikhouse.fr` / `Tenant123!`
