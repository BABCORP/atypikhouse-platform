# AtypikHouse Platform

AtypikHouse est un MVP académique fictif de marketplace de réservation d’hébergements insolites : cabanes perchées, yourtes, cabanes flottantes, tiny houses, dômes et logements nature.

**Projet étudiant fictif. Aucun achat, paiement ou réservation réelle ne peut être effectué. Les informations présentées sont utilisées dans le cadre d’une démonstration académique.**

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

## Déploiement Render

Le projet est prêt pour Render via Docker :

- `Dockerfile` : PHP 8.3 + Apache + `pdo_mysql`.
- `public/` est le document root.
- `render.yaml` propose un Web Service Docker.
- `database/render-update.sql` contient une mise à jour non destructive pour une base MySQL déjà existante.

La procédure complète, les variables d’environnement Render et les commandes d’import MySQL sont dans `docs/render-deploy.md`.

## Identifiants et services externes

Les comptes de démonstration, les placeholders Google Analytics, réseaux sociaux, emailing et SMTP sont centralisés dans `docs/identifiants-acces.md`.

Pour préparer un environnement local :

```bash
cp .env.example .env
```

Renseigner ensuite les valeurs de base de données dans `.env`. Les secrets réels ne doivent jamais être commitées.

Configuration SMTP Google prévue :

```text
MAIL_MAILER=smtp
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
SMTP_USERNAME=
SMTP_PASSWORD=
SMTP_FROM_EMAIL=contact@atypikhouse.fr
SMTP_FROM_NAME=AtypikHouse
```

Pour la démonstration académique, conserver :

```text
MAIL_DEMO_MODE=true
MAIL_LOG_ONLY=true
```

En mode démonstration, les emails transactionnels sont journalisés localement dans `storage/logs/mail-demo.log` et ne sont pas envoyés réellement. Pour tester un SMTP réel plus tard, renseigner `SMTP_USERNAME` et `SMTP_PASSWORD`, puis passer `MAIL_DEMO_MODE=false` et `MAIL_LOG_ONLY=false`.

Les placeholders `GA4_ID`, `GTM_ID`, `INSTAGRAM_URL`, `FACEBOOK_URL`, `EMAILING_PROVIDER`, `EMAILING_API_KEY` et `EMAILING_LIST_ID` sont documentés dans `.env.example`. Aucun identifiant réel n’est stocké dans le dépôt.

## Comptes de démonstration

- Admin : `admin@atypikhouse.fr` / `Admin123!`
- Propriétaire : `proprietaire@atypikhouse.fr` / `Owner123!`
- Locataire : `locataire@atypikhouse.fr` / `Tenant123!`

## Fonctionnalités principales

- Pages publiques : accueil, concept, catalogue, détail logement, blog, FAQ, contact, devenir hôte, pages légales.
- Authentification avec mots de passe hashés via `password_hash`.
- Contrôle d’accès par rôle : locataire, propriétaire, administrateur.
- Validation administrateur des nouveaux comptes : les comptes créés passent en attente avant usage complet.
- Catalogue avec filtres destination assistée par liste de destinations publiées, type, capacité, prix, équipement et dates.
- Réservation fictive avec vérification dates, capacité, chevauchement et dates passées, puis validation administrateur avant paiement de démonstration.
- Calendrier public léger sur les fiches logements pour visualiser dates réservées, indisponibles et prix spécifiques.
- Paiement simulé sans collecte de carte bancaire réelle.
- Avis modérés par administrateur avec affichage en étoiles.
- Favoris : cœur sur les logements, ajout/retrait protégé par CSRF et page locataire dédiée.
- Dashboard locataire : réservations, détail, favoris, avis, profil.
- Dashboard propriétaire : logements, création/modification détaillée, upload image contrôlé, soumission en attente de validation, modifications de logements publiés soumises à validation admin, disponibilités, réservations.
- Dashboard propriétaire : galerie d’images avec texte alternatif, image principale, suppression sécurisée et gestion de plages de disponibilité avec prix spécifique.
- Calendrier mensuel propriétaire en JavaScript vanilla pour visualiser disponibilités, indisponibilités, prix spécifiques et dates réservées.
- Dashboard administrateur : statistiques détaillées, comptes à valider, logements à valider, réservations à confirmer, paiements fictifs en attente, derniers logs, avis et messages.
- Back-office administrateur démontrable : recherche utilisateurs, détail utilisateur, suspension/réactivation, gestion profils hôtes, validation complète des logements, comparaison et validation des modifications proposées par les propriétaires, détail et édition admin des logements, pause/réactivation/suppression logique des logements, filtres réservations, modération avis, publication/dépublication blog, messages lus/traités/archivés et logs d’audit.
- Pagination simple et filtres sur plusieurs listes administrateur : utilisateurs, logements, réservations, messages et logs.
- Réinitialisation de mot de passe locale de démonstration, sans envoi email réel.
- Reçu/facture fictive imprimable sur le détail de réservation locataire.
- Pages d’erreur 403/404 propres et confirmations JS sur actions destructives.
- Contact avec protection CSRF.
- Fichiers SEO : `robots.txt`, `sitemap.xml`, canonical, Open Graph, JSON-LD blog/logement et URLs propres.
- Structure tracking-ready avec attributs `data-track`, `dataLayer`, placeholders `GA4_ID`/`GTM_ID` et consentement cookies.
- Formulaire `/mes-donnees` pour simuler les demandes RGPD : accès, rectification, suppression et opposition.
- Identité visuelle alignée sur la charte AtypikHouse 2026 et photographies de démonstration issues des maquettes fournies.
- Données de démonstration enrichies : 12 logements publiés, locataires fictifs, réservations terminées, favoris et avis publiés pour rendre chaque fiche logement crédible.
- Contenus logements harmonisés avec les visuels : titres affichés, descriptions, équipements et textes alternatifs ont été réécrits pour rester cohérents avec les photos locales.

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

Les slugs de certains logements historiques sont conservés pour éviter de casser les URLs de démonstration, même lorsque le titre affiché a été ajusté pour mieux correspondre au visuel.

Les avis présents dans le seed sont entièrement fictifs et servent uniquement à la démonstration académique. Ils ne correspondent à aucun vrai client ni à aucune réservation réelle.

La suppression admin d’un logement est logique : le statut passe à `deleted`, les réservations, avis, images et logs restent conservés pour l’historique de démonstration.

Lorsqu’un propriétaire modifie un logement déjà publié ou mis en pause, la version publique n’est pas écrasée directement. Une demande est stockée dans `property_change_requests` avec les nouvelles valeurs proposées, puis l’administrateur compare, approuve ou refuse la modification depuis le back-office. Les images ajoutées dans une demande refusée peuvent rester stockées localement comme artefacts de démonstration.

Limites assumées : pas d’email réel, pas de paiement réel, pas de passerelle Brevo/Stripe, pas de calendrier planning professionnel avec drag-and-drop, pas de système de suppression RGPD automatisé complet, pas de workflow de notification automatique propriétaire après refus/pause. Ces points sont simulés ou documentés pour la soutenance.

Les vérifications réalisées sur MAMP sont détaillées dans `docs/validation-report.md`.

Un guide de smoke test local est disponible dans `docs/smoke-test.md`.
