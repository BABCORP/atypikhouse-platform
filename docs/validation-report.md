# Rapport de validation locale

Date de validation : 4 juillet 2026
Environnement : MAMP, PHP 8.3.30, MySQL 8.0.44

## Vérifications exécutées

- Lint PHP sur l’ensemble des fichiers `app/`, `config/`, `public/` et `routes/` : aucun défaut de syntaxe.
- Import réel de `database/schema.sql` et `database/seed.sql`.
- Contrôle du seed : 11 utilisateurs fictifs, 12 logements publiés, 15 images de démonstration, 27 réservations de démonstration et 26 avis publiés.
- Chargement HTTP des pages accueil, catalogue filtré, fiche logement, FAQ et connexion : réponses `200`.
- Connexion locataire avec le compte de démonstration et accès à son dashboard.
- Réservation fictive et paiement test réussi : passage de `pending_payment` à `confirmed` avec `payment_status=test_paid`.
- Blocage d’une réservation chevauchant une réservation confirmée.
- Connexion propriétaire, affichage du dashboard et formulaire de logement.
- Upload PNG propriétaire : stockage hors dossier public et restitution via endpoint média contrôlé.
- Connexion administrateur, affichage des statistiques, modération des avis et CRUD blog.
- Test CSRF sans jeton : requête refusée avec le statut HTTP `419`.
- Intégration des visuels fournis dans la maquette Figma locale et alignement de l’interface avec la charte 2026 (ivoire, vert forêt, accent terracotta, titrage serif).
- Vérification visuelle mobile dans le navigateur intégré : hero photo lisible, cartes catalogue illustrées, fiche avec galerie, contact et blog illustrés.
- Vérification UX mobile du catalogue : les filtres avancés restent accessibles dans un volet repliable et ne masquent pas la liste de logements.
- Vérification du formulaire contact : consentement requis côté interface et contrôle correspondant côté serveur.
- Ajout et vérification prévue des écrans admin : édition utilisateur et détail réservation.
- Ajout et vérification prévue de la galerie propriétaire : alt text, image principale, suppression et placeholder.
- Ajout et vérification prévue des disponibilités par plage, prix override et calcul de total nuit par nuit.
- Ajout du calendrier propriétaire en JavaScript vanilla : légende, navigation mensuelle, dates passées/réservées désactivées et affichage du prix spécifique.
- Ajout et vérification prévue du reset password local de démonstration.
- Ajout des canonical, Open Graph, JSON-LD, sitemap local, robots.txt local, consentement cookies et placeholders analytics.
- Ajout d’un service `NewsletterService` Brevo-ready en mode démo sécurisé.
- Ajout du formulaire `/mes-donnees` pour demandes RGPD simulées, visibles dans les messages admin.
- Ajout de headers de sécurité légers et maintien CSRF/RBAC/ownership.
- Ajout `.env.example`, pages d’erreur personnalisées, confirmations destructives et reçu fictif imprimable.
- Ajout de la pagination/filtres sur utilisateurs, réservations, messages et journaux d’audit.
- Ajout du calendrier public de disponibilité sur les fiches logements.
- Ajout du guide `docs/smoke-test.md`.
- Remplacement des visuels incohérents de démonstration, notamment l'image tropicale/piscine précédemment utilisée sur des logements en France, par des fichiers locaux plus crédibles fournis pour la maquette : cabane bois avec spa, hébergement insolite en jardin, tiny house/pod bois et dôme nature.
- Harmonisation éditoriale des logements seedés : titres visibles, types, descriptions courtes/longues, équipements et textes alternatifs ont été ajustés pour mieux correspondre aux photographies locales.
- Ajout de 4 logements fictifs supplémentaires à partir des visuels fournis : cabane flottante sur l’étang, dôme vitré au milieu des vergers, kota familial en pleine campagne et lodge en bois avec bain nordique.
- Ajout d'avis clients fictifs publiés pour chaque logement seedé, associés à des locataires de démonstration et à des réservations passées terminées.

## Remise à zéro

Après les tests mutables, le schéma et le seed ont été réimportés afin de restituer un environnement de démonstration propre.

## Limites de démonstration

- Les logements seedés utilisent des photographies de démonstration locales fournies pour le projet académique. Les droits et sources exactes de ces visuels devront être confirmés avant toute mise en production réelle.
- Certains slugs historiques sont conservés pour ne pas casser les URLs de démonstration, même lorsque le titre affiché a été renommé pour coller au visuel.
- Les avis seedés sont fictifs, créés uniquement pour rendre la démonstration plus réaliste ; ils ne représentent aucun vrai client ni aucune vraie réservation.
- Le paiement est exclusivement fictif et ne traite aucune donnée bancaire.
- Les identifiants GA4/GTM restent volontairement vides par défaut.
- L’emailing/newsletter est uniquement stocké localement ; aucune intégration Brevo réelle n’est branchée en V1.
- Le calendrier est interactif pour la visualisation et le préremplissage, mais ne remplace pas le formulaire serveur de plages qui reste la source sûre.
- Les demandes RGPD sont simulées et stockées localement ; aucune suppression automatique réelle n’est exécutée.
