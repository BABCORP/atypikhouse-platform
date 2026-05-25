# AtypikHouse Platform

AtypikHouse est un MVP académique fictif de marketplace de réservation d’hébergements insolites : cabanes perchées, yourtes, cabanes flottantes, tiny houses, dômes et logements nature.

**Projet étudiant fictif — aucun achat, paiement ou réservation réelle ne peut être effectué.**

## Stack

- PHP 8.2+
- MySQL 8.0+
- PDO avec requêtes préparées
- HTML5, CSS3, JavaScript ES6
- Architecture MVC légère sans framework, sans CMS, sans thème préconçu

## Installation locale

1. Cloner le dépôt.
2. Configurer un serveur local type WAMP, MAMP, XAMPP ou serveur PHP intégré.
3. Créer la base et importer les données :

```bash
mysql -u root -p < database/schema.sql
mysql -u root -p atypikhouse < database/seed.sql
```

4. Adapter si besoin `config/database.php` ou définir les variables d’environnement :

```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atypikhouse
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://localhost:8000
```

Configuration habituelle avec MAMP :

```bash
export DB_HOST=127.0.0.1
export DB_PORT=8889
export DB_DATABASE=atypikhouse
export DB_USERNAME=root
export DB_PASSWORD=root
export DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock
export APP_URL=http://localhost:8000
```

5. Lancer en local depuis la racine :

```bash
php -S localhost:8000 -t public public/index.php
```

## Comptes de démonstration

- Admin : `admin@atypikhouse.test` / `Admin123!`
- Propriétaire : `owner@atypikhouse.test` / `Owner123!`
- Locataire : `tenant@atypikhouse.test` / `Tenant123!`

## Fonctionnalités principales

- Pages publiques : accueil, concept, catalogue, détail logement, blog, FAQ, contact, devenir hôte, pages légales.
- Authentification avec mots de passe hashés via `password_hash`.
- Contrôle d’accès par rôle : locataire, propriétaire, administrateur.
- Catalogue avec filtres destination, type, capacité, prix, équipement et dates.
- Réservation fictive avec vérification dates, capacité, chevauchement et dates passées.
- Paiement simulé sans collecte de carte bancaire réelle.
- Avis modérés par administrateur.
- Dashboard locataire : réservations, détail, avis, profil.
- Dashboard propriétaire : logements, création/modification, upload image contrôlé, soumission, disponibilités, réservations.
- Dashboard administrateur : statistiques, utilisateurs, propriétaires, logements, réservations, avis, CRUD blog, messages, logs.
- Contact avec protection CSRF.
- Fichiers SEO : `robots.txt`, `sitemap.xml`, métadonnées et URLs propres.
- Structure tracking-ready avec attributs `data-track`.
- Identité visuelle alignée sur la charte AtypikHouse 2026 et photographies de démonstration issues des maquettes fournies.

## Structure

```text
app/
  Controllers/
  Core/
  Helpers/
  Models/
  Views/
config/
database/
docs/
public/
  assets/
  index.php
routes/
storage/
```

## Sécurité

- PDO et requêtes préparées.
- Échappement de sortie avec `htmlspecialchars`.
- Protection CSRF sur les formulaires sensibles.
- Durcissement de session et régénération d’ID après connexion.
- Vérifications RBAC côté serveur.
- Vérification de propriété des ressources pour les propriétaires et locataires.
- Limitation simple des tentatives de connexion.
- Upload d’images limité à JPG/PNG/WEBP, 5 Mo maximum, fichiers stockés dans `storage/uploads`.
- Audit logs pour actions sensibles.
- Aucun paiement réel, aucun numéro de carte réel.

## Notes MVP

Le projet est volontairement léger pour rester compatible avec un environnement PHP local académique. Le jeu de démonstration inclut des visuels de maquette ; si un propriétaire ne téléverse aucune image, le MVP utilise un placeholder accessible.

Les vérifications réalisées sur MAMP sont détaillées dans `docs/validation-report.md`.
