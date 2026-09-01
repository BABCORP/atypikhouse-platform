# Rapport de validation locale

Date de validation : 4 juillet 2026
Environnement : MAMP, PHP 8.3.30, MySQL 8.0.44

Mise à jour complémentaire : 10 juillet 2026

## Vérifications exécutées

- Lint PHP sur l’ensemble des fichiers `app/`, `config/`, `public/` et `routes/` : aucun défaut de syntaxe.
- Import réel de `database/schema.sql` et `database/seed.sql`.
- Contrôle du seed : 11 utilisateurs fictifs, 12 logements publiés, 15 images de démonstration, 27 réservations de démonstration et 26 avis publiés.
- Chargement HTTP des pages accueil, catalogue filtré, fiche logement, FAQ et connexion : réponses `200`.
- Connexion locataire avec le compte de démonstration et accès à son dashboard.
- Réservation fictive et paiement fictif de démonstration réussi : passage de `pending_payment` à `confirmed` avec `payment_status=test_paid`.
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
- Renforcement du back-office administrateur pour la soutenance : dashboard enrichi, recherche/détail utilisateur, suspension/réactivation, filtres propriétaires/logements/réservations/avis, détail et édition admin des logements, actions rapides de publication/refus/pause/réactivation/suppression logique, annulation/terminaison réservation, publication/dépublication blog, détail message et statut “traité”.
- Ajout de logs d’audit explicites pour les actions admin sensibles : `user_suspended`, `user_reactivated`, `property_approved`, `property_rejected`, `property_paused`, `property_reactivated`, `property_deleted`, `property_updated_by_admin`, `booking_cancelled_by_admin`, `booking_completed_by_admin`, `review_approved`, `review_rejected`, `review_deleted`, `blog_created`, `blog_updated`, `blog_published`, `blog_unpublished`, `blog_deleted`, `message_read`, `message_processed`, `message_archived`.
- Ajout UX locataire : favoris par logement avec bouton cœur, page `/locataire/favoris`, affichage des notes et avis en étoiles accessibles, et champ Destination avec liste de localisations issues uniquement des logements publiés.
- Harmonisation du disclaimer académique avec la formulation sans tiret long : “Projet étudiant fictif. Aucun achat, paiement ou réservation réelle ne peut être effectué. Les informations présentées sont utilisées dans le cadre d’une démonstration académique.”
- Ajustement du workflow de validation des comptes : les nouveaux comptes locataires sont créés directement en `active`, tandis que les comptes propriétaires restent en `pending` et doivent être approuvés/refusés/suspendus/réactivés depuis `/admin/utilisateurs`.
- Renforcement du workflow propriétaire : seul un propriétaire validé peut accéder à l’ajout de logement, et toute création propriétaire passe en `pending` avant publication admin.
- Ajout du workflow de modification différée : lorsqu’un propriétaire modifie un logement déjà publié ou en pause, les nouvelles données sont stockées dans `property_change_requests` et la version publique reste inchangée tant que l’admin n’a pas approuvé la demande.
- Ajout de l’écran admin `/admin/logements/modifications` et du détail de comparaison ancien/nouveau avec actions d’approbation et de refus.
- Test local du workflow modification : propriétaire démo change le prix d’un logement publié de `185.00` à `210.00`, la table `properties` reste à `185.00`, la demande pending contient `210`, puis l’approbation admin publie temporairement `210` avant restauration du logement de démonstration à `185.00`.
- Renforcement du workflow réservation : une réservation locataire active est créée en `pending_admin` avec paiement `not_paid`, apparaît dans `/admin/reservations`, puis peut être confirmée par l’administrateur avant paiement fictif.
- Enrichissement du dashboard admin : compteurs de comptes à valider, logements à valider/en pause/supprimés, réservations à valider/confirmées/annulées/terminées, paiements fictifs, messages et avis.
- Formulaire propriétaire “Ajouter un logement” restructuré en sections : informations principales, localisation, capacité, tarification, équipements, images et rappel de validation admin.

## Remise à zéro

Après les tests mutables, le schéma et le seed ont été réimportés afin de restituer un environnement de démonstration propre.

## Limites de démonstration

- Les logements seedés utilisent des photographies de démonstration locales fournies pour le projet académique. Les droits et sources exactes de ces visuels devront être confirmés avant toute mise en production réelle.
- Certains slugs historiques sont conservés pour ne pas casser les URLs de démonstration, même lorsque le titre affiché a été renommé pour coller au visuel.
- Les avis seedés sont fictifs, créés uniquement pour rendre la démonstration plus réaliste ; ils ne représentent aucun vrai client ni aucune vraie réservation.
- Le paiement est exclusivement fictif et ne traite aucune donnée bancaire.
- Les identifiants GA4/GTM restent volontairement vides par défaut.
- La newsletter marketing est stockée localement par défaut ; les emails transactionnels peuvent être envoyés via Brevo SMTP lorsque les variables Render sont configurées.
- Le calendrier est interactif pour la visualisation et le préremplissage, mais ne remplace pas le formulaire serveur de plages qui reste la source sûre.
- Les demandes RGPD sont simulées et stockées localement ; aucune suppression automatique réelle n’est exécutée.
- Le back-office est complet pour un MVP académique ; les notifications email transactionnelles principales passent par Brevo SMTP si l’envoi réel est activé.
- Les validations admin conservent les messages flash et les statuts en base ; les notifications internes admin restent journalisées pour éviter de multiplier les envois SMTP.
- La suppression de logement est volontairement logique (`status=deleted`) pour ne pas casser les réservations, avis et historiques liés.
- Les images téléversées dans une demande de modification refusée ou remplacée peuvent rester présentes dans `storage/uploads` comme artefacts locaux ; aucun nettoyage automatique avancé n’est déclenché dans ce MVP.
- Les favoris sont stockés localement pour la démonstration et ne déclenchent aucune notification ni recommandation automatisée.
