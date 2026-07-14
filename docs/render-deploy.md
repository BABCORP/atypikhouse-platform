# Déploiement Render

Ce projet PHP/MySQL se déploie sur Render en **Web Service Docker**. Render ne fournit pas de base MySQL managée native pour ce MVP ; il faut donc utiliser une base MySQL 8 externe, puis connecter le web service avec des variables d’environnement.

## 1. Service Render

Créer un Web Service depuis le dépôt GitHub :

- Branch : `clean/atypikhouse-mvp`
- Runtime : Docker
- Dockerfile : `./Dockerfile`
- Health check path conseillé : `/`

Le conteneur sert `public/` avec Apache et utilise le port injecté par Render via `PORT`.

## 2. Variables d’environnement

Variables obligatoires :

```text
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-service.onrender.com
DATABASE_URL=mysql://USER:PASSWORD@HOST:3306/DATABASE
BREVO_ENABLED=false
```

Alternative si votre hébergeur MySQL ne donne pas d’URL complète :

```text
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...
DB_SOCKET=
```

Variables optionnelles :

```text
MYSQL_URL=mysql://USER:PASSWORD@HOST:3306/DATABASE
DB_SSL_CA=/chemin/ca.pem
DB_SSL_VERIFY=false
GA4_ID=
GTM_ID=
BREVO_API_KEY=
BREVO_LIST_ID=
```

Ne pas mettre `.env` dans GitHub. Les secrets doivent rester dans Render.

## 3. Base de données

Pour une base vide :

```bash
mysql -h HOST -P 3306 -u USER -p DATABASE < database/schema.sql
mysql -h HOST -P 3306 -u USER -p DATABASE < database/seed.sql
```

Pour une base déjà existante avec les données à conserver :

```bash
mysql -h HOST -P 3306 -u USER -p DATABASE < database/render-update.sql
```

Important : `database/schema.sql` supprime et recrée les tables. Ne pas l’utiliser sur une base de production contenant des données à conserver.

## 4. Uploads

Les images téléversées par les propriétaires sont stockées dans `storage/uploads`. Sur Render, le stockage du conteneur peut être éphémère après redéploiement. Pour une démonstration courte, les images seedées dans `public/assets/img/properties` restent stables. Pour conserver les uploads en continu, ajouter un Disk Render monté sur :

```text
/var/www/html/storage/uploads
```

## 5. Comptes de démonstration

Après import du seed :

- Admin : `admin@atypikhouse.fr` / `Admin123!`
- Propriétaire : `proprietaire@atypikhouse.fr` / `Owner123!`
- Locataire : `locataire@atypikhouse.fr` / `Tenant123!`

## 6. Vérifications après déploiement

- Ouvrir `/`.
- Ouvrir `/hebergements`.
- Se connecter en admin et ouvrir `/admin/dashboard`.
- Se connecter en propriétaire et ouvrir `/proprietaire/logements`.
- Vérifier qu’un logement publié peut être modifié sans changer immédiatement la fiche publique.
- Vérifier `/admin/logements/modifications`.

Le site reste un projet étudiant fictif : aucun paiement réel, aucune réservation réelle et aucun email réel ne sont envoyés.
