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

Un fichier `.env.example` est fourni comme modèle de configuration locale.

```bash
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=atypikhouse
DB_USERNAME=root
DB_PASSWORD=
APP_URL=http://127.0.0.1:8010
```

Configuration habituelle avec MAMP :

```bash
export DB_HOST=127.0.0.1
export DB_PORT=8889
export DB_DATABASE=atypikhouse
export DB_USERNAME=root
export DB_PASSWORD=root
export DB_SOCKET=/Applications/MAMP/tmp/mysql/mysql.sock
export APP_URL=http://127.0.0.1:8010
```

5. Lancer en local depuis la racine :

```bash
php -S 127.0.0.1:8010 -t public public/index.php
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
- Calendrier public léger sur les fiches logements pour visualiser dates réservées, indisponibles et prix spécifiques.
- Paiement simulé sans collecte de carte bancaire réelle.
- Avis modérés par administrateur.
- Dashboard locataire : réservations, détail, avis, profil.
- Dashboard propriétaire : logements, création/modification, upload image contrôlé, soumission, disponibilités, réservations.
- Dashboard propriétaire : galerie d’images avec texte alternatif, image principale, suppression sécurisée et gestion de plages de disponibilité avec prix spécifique.
- Calendrier mensuel propriétaire en JavaScript vanilla pour visualiser disponibilités, indisponibilités, prix spécifiques et dates réservées.
- Dashboard administrateur : statistiques, édition complète des utilisateurs, propriétaires, logements, détail réservation, avis, CRUD blog, messages, logs.
- Pagination simple et filtres sur plusieurs listes administrateur : utilisateurs, réservations, messages et logs.
- Réinitialisation de mot de passe locale de démonstration, sans envoi email réel.
- Reçu/facture fictive imprimable sur le détail de réservation locataire.
- Pages d’erreur 403/404 propres et confirmations JS sur actions destructives.
- Contact avec protection CSRF.
- Fichiers SEO : `robots.txt`, `sitemap.xml`, canonical, Open Graph, JSON-LD blog/logement et URLs propres.
- Structure tracking-ready avec attributs `data-track`, `dataLayer`, placeholders `GA4_ID`/`GTM_ID` et consentement cookies.
- Formulaire `/mes-donnees` pour simuler les demandes RGPD : accès, rectification, suppression et opposition.
- Identité visuelle alignée sur la charte AtypikHouse 2026 et photographies de démonstration issues des maquettes fournies.
- Données de démonstration enrichies : locataires fictifs, réservations terminées et avis publiés pour rendre chaque fiche logement crédible.

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
- Headers de sécurité légers : `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Permissions-Policy`.
- Aucun paiement réel, aucun numéro de carte réel.

## Données, cookies et analytics

Les formulaires contact/newsletter stockent des données uniquement dans la base locale de démonstration. La newsletter réutilise `contact_messages` avec le sujet `Newsletter`; aucune intégration Brevo ni aucun email réel n’est déclenché.

Variables prévues pour une future intégration Brevo :

```bash
BREVO_API_KEY=
BREVO_LIST_ID=
BREVO_ENABLED=false
```

Même si `BREVO_ENABLED=true`, le MVP garde une protection académique : la structure d’intégration est prête, mais aucun appel réseau réel n’est déclenché par défaut.

Le bandeau cookies mémorise le choix dans `localStorage`. GA4/GTM ne sont chargés que si un identifiant est fourni par variable d’environnement et si le consentement est accepté.

## Notes MVP

Le projet est volontairement léger pour rester compatible avec un environnement PHP local académique. Le jeu de démonstration inclut des visuels de maquette ; si un propriétaire ne téléverse aucune image, le MVP utilise un placeholder accessible.

Les avis présents dans le seed sont entièrement fictifs et servent uniquement à la démonstration académique. Ils ne correspondent à aucun vrai client ni à aucune réservation réelle.

Limites assumées : pas d’email réel, pas de paiement réel, pas de passerelle Brevo/Stripe, pas de calendrier planning professionnel avec drag-and-drop, pas de système de suppression RGPD automatisé complet. Ces points sont simulés ou documentés pour la soutenance.

Les vérifications réalisées sur MAMP sont détaillées dans `docs/validation-report.md`.

Un guide de smoke test local est disponible dans `docs/smoke-test.md`.
