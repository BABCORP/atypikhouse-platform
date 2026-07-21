# Identifiants et accès de livraison

## 1. Avertissement de sécurité

Ce document centralise les accès nécessaires à la démonstration du projet AtypikHouse. Les identifiants sensibles réels ne doivent jamais être versionnés dans le dépôt Git. Les valeurs techniques réelles doivent être renseignées localement dans le fichier `.env` à partir du fichier `.env.example`.

Le fichier contient uniquement des comptes de démonstration utilisables ou des placeholders. Les vrais accès Google, SMTP, réseaux sociaux, emailing et analytics doivent être transmis par un canal sécurisé, jamais par GitHub, jamais dans le code, jamais dans les vues publiques.

AtypikHouse reste un projet étudiant fictif. Aucun achat, paiement, email commercial ou réservation réelle ne peut être effectué dans le cadre de cette démonstration académique.

## 2. Back-office administrateur

Ces identifiants correspondent au seed de démonstration et fonctionnent après import de `database/schema.sql` puis `database/seed.sql`.

| Rôle | URL | Email | Mot de passe | Statut | Utilisation |
| --- | --- | --- | --- | --- | --- |
| Admin | `/connexion` | `admin@atypikhouse.fr` | `Admin123!` | Actif | Accès au back-office administrateur |

Accès direct après connexion : `/admin/dashboard`.

## 3. Comptes de démonstration supplémentaires

| Rôle | URL | Email | Mot de passe | Statut | Parcours de test |
| --- | --- | --- | --- | --- | --- |
| Locataire | `/connexion` | `locataire@atypikhouse.fr` | `Tenant123!` | Actif | Réserver, paiement fictif, favoris, avis |
| Propriétaire | `/connexion` | `proprietaire@atypikhouse.fr` | `Owner123!` | Actif | Ajouter logement, gérer images, disponibilités |
| Admin | `/connexion` | `admin@atypikhouse.fr` | `Admin123!` | Actif | Valider comptes, logements, réservations |

Correspondance des rôles internes :

- `tenant` = locataire / voyageur ;
- `owner` = propriétaire / hôte ;
- `admin` = administrateur.

## 4. Google Analytics / GA4

- Plateforme : Google Analytics 4
- Compte Google : `PLACEHOLDER_GOOGLE_ACCOUNT_EMAIL`
- Propriété GA4 : `AtypikHouse`
- GA4 Measurement ID : `G-XXXXXXXXXX`
- Accès : à renseigner dans `.env` avec `GA4_ID`
- Statut : placeholder, aucun identifiant réel par défaut

Le tracking Google Analytics ne doit se charger que si un identifiant GA4 valide est renseigné et si le consentement cookies est accepté.

## 5. Réseaux sociaux

| Réseau social | URL | Identifiant / email | Mot de passe | Remarque |
| --- | --- | --- | --- | --- |
| Instagram | `https://www.instagram.com/atypikhouse__off/` | `PLACEHOLDER_INSTAGRAM_LOGIN` | `PLACEHOLDER_SECURE_PASSWORD` | Compte vitrine |
| Facebook | `PLACEHOLDER_FACEBOOK_URL` | `PLACEHOLDER_FACEBOOK_LOGIN` | `PLACEHOLDER_SECURE_PASSWORD` | À compléter si compte créé |

Ne pas déposer de vrai mot de passe ou token social dans ce document.

## 6. Prestataire emailing / newsletter

| Service | URL | Identifiant | API Key | Statut |
| --- | --- | --- | --- | --- |
| Brevo / Mailchimp / Bluejet | `PLACEHOLDER_PROVIDER_URL` | `PLACEHOLDER_EMAILING_LOGIN` | `PLACEHOLDER_API_KEY` | Placeholder |

L’intégration emailing externe est prévue comme structure de configuration. Aucun envoi marketing réel n’est effectué par défaut.

Variables prévues :

```text
EMAILING_PROVIDER=
EMAILING_API_KEY=
EMAILING_LIST_ID=
BREVO_API_KEY=
BREVO_LIST_ID=
BREVO_ENABLED=false
```

## 7. SMTP Google — Envoi d’emails transactionnels

Valeurs placeholders à placer dans `.env` :

```text
SMTP_ENABLED=false
REAL_EMAIL_SENDING=false
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_ENCRYPTION=tls
MAIL_USERNAME=your-google-account@example.com
MAIL_PASSWORD=your-google-app-password
MAIL_FROM_ADDRESS=contact@atypikhouse.fr
MAIL_FROM_NAME=AtypikHouse
ADMIN_EMAIL=admin@atypikhouse.fr
SUPPORT_EMAIL=support@atypikhouse.fr
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_ENCRYPTION=tls
SMTP_USERNAME=your-google-account@example.com
SMTP_PASSWORD=your-google-app-password
SMTP_FROM_EMAIL=contact@atypikhouse.fr
SMTP_FROM_NAME=AtypikHouse
ADMIN_NOTIFICATION_EMAIL=contact@atypikhouse.fr
MAIL_DEMO_MODE=true
MAIL_LOG_ONLY=true
```

Le mot de passe SMTP ne doit jamais être commité. Si Google SMTP impose un mot de passe d’application, il doit être généré depuis les paramètres de sécurité du compte Google.

Si SMTP n’est pas configuré, l’application ne doit pas planter. En mode démonstration, les emails sont journalisés dans `storage/logs/mail-demo.log` et ne sont pas envoyés réellement.

Pour tester un envoi réel plus tard :

```text
SMTP_ENABLED=true
REAL_EMAIL_SENDING=true
MAIL_DEMO_MODE=false
MAIL_LOG_ONLY=false
MAIL_USERNAME=...
MAIL_PASSWORD=...
```

## 8. URLs utiles

- Site local MAMP courant : `http://127.0.0.1:8010`
- Site local MAMP dossier public : `http://localhost:8888/atypikhouse/public`
- Connexion : `/connexion`
- Admin dashboard : `/admin/dashboard`
- Catalogue : `/hebergements`
- Contact : `/contact`
- Documentation Render : `docs/render-deploy.md`

## 9. Checklist de livraison

- `.env` absent du dépôt Git.
- `.env.example` contient uniquement des placeholders.
- Les comptes de démonstration sont présents dans `database/seed.sql`.
- Le mode mail démo reste activé par défaut.
- Aucun mot de passe SMTP, token analytics ou clé API réelle n’est versionné.
