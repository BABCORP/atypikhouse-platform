# Tests fonctionnels AtypikHouse

## Authentification

- Se connecter avec `admin@atypikhouse.fr` / `Admin123!`, vérifier l’accès à `/admin/dashboard`.
- Se connecter avec `proprietaire@atypikhouse.fr` / `Owner123!`, vérifier l’accès à `/proprietaire/dashboard`.
- Se connecter avec `locataire@atypikhouse.fr` / `Tenant123!`, vérifier l’accès à `/locataire/dashboard`.
- Créer un compte locataire depuis `/inscription`, vérifier la redirection vers `/connexion`.
- Créer un compte propriétaire depuis `/inscription?role=owner`, vérifier la création du profil propriétaire.
- Vérifier qu’un nouveau compte est créé en statut `pending`, puis l’approuver depuis `/admin/utilisateurs`.
- Vérifier qu’un compte locataire `pending` ne peut pas réserver.
- Vérifier qu’un compte propriétaire `pending` ne peut pas ajouter de logement.
- Essayer une connexion avec un mauvais mot de passe, vérifier le message d’erreur.
- Passer un utilisateur en `suspended` depuis l’admin, vérifier qu’il ne peut plus se connecter.

## Identifiants et emails de démonstration

- Ouvrir `docs/identifiants-acces.md` et vérifier la présence des sections admin, comptes de démonstration, GA4, réseaux sociaux, emailing, SMTP Brevo et URLs utiles.
- Vérifier que `.env` est ignoré par Git et que `.env.example` ne contient que des placeholders.
- Vérifier que `MAIL_DEMO_MODE=true` et `MAIL_LOG_ONLY=true` empêchent tout envoi réel.
- Créer un compte depuis `/inscription`, vérifier que `storage/logs/mail-demo.log` contient une entrée sans secret SMTP.
- Soumettre un logement propriétaire, vérifier une entrée mail de démonstration.
- Créer une réservation locataire, vérifier une entrée mail de démonstration.
- Soumettre le formulaire contact, vérifier une entrée mail de démonstration.
- Vérifier dans `/admin/logs` la présence d’événements `mail_demo_logged` après les actions précédentes.
- Passer temporairement `MAIL_DEMO_MODE=false` et `MAIL_LOG_ONLY=false` uniquement dans un environnement local sécurisé avec SMTP configuré, puis revenir immédiatement au mode démo.
- Sur Render, vérifier `/admin/dashboard` : le serveur SMTP doit afficher `smtp-relay.brevo.com` et le fallback Brevo doit indiquer `2525/tls, 465/ssl`.
- Sur Render, envoyer un email de test depuis `/admin/dashboard` et vérifier un événement `mail_send_success` dans les logs.

## Catalogue

- Ouvrir `/hebergements`, vérifier que seuls les logements `published` apparaissent.
- Filtrer par type `treehouse`, vérifier que seules les cabanes remontent.
- Filtrer par prix maximum, vérifier que les logements plus chers sont exclus.
- Filtrer par capacité, vérifier que les logements trop petits sont exclus.
- Filtrer par équipement et dates disponibles.
- Cliquer dans le champ Destination, vérifier que la datalist propose uniquement des destinations de logements publiés, puis lancer une recherche.
- Vérifier que les cartes logements affichent un bouton cœur accessible et une note moyenne en étoiles.
- Ouvrir une fiche logement, vérifier galerie, description, équipements, avis et formulaire de réservation.
- Ouvrir une fiche logement, vérifier le bouton cœur, la note moyenne en étoiles, le nombre d’avis et les étoiles de chaque avis.
- Vérifier que chaque titre de logement, description courte, description longue, équipements et texte alternatif correspondent au visuel principal affiché.
- Vérifier que chaque fiche logement seedée affiche au moins deux avis publiés et n’affiche pas “Aucun avis publié pour le moment”.
- Vérifier le calendrier public : dates passées désactivées, dates indisponibles/réservées bloquées, prix override visible, clic sur une date valide préremplit arrivée/départ.

## Favoris

- Non connecté, cliquer sur un cœur depuis `/hebergements`, vérifier la redirection vers `/connexion` avec un message clair.
- Connecté locataire, ajouter un logement aux favoris depuis une carte catalogue.
- Vérifier que le cœur passe en état rempli.
- Retirer le même logement des favoris et vérifier que le cœur redevient vide.
- Ajouter un logement aux favoris depuis une fiche logement.
- Ouvrir `/locataire/favoris`, vérifier que seuls les logements favoris publiés du locataire apparaissent.
- Vérifier qu’un même logement ne peut pas être ajouté deux fois.
- Vérifier que l’action POST sans jeton CSRF est refusée.

## Réservation

- Connecté locataire, réserver des dates futures disponibles.
- Vérifier que la réservation est créée en `pending_admin` avec paiement `not_paid`, puis visible dans `/admin/reservations?status=pending_admin`.
- Depuis l’admin, valider la demande et vérifier qu’elle passe en `pending_payment` sans 403/404.
- Vérifier que le paiement fictif n’est accessible qu’après validation admin et avant confirmation définitive.
- Tenter une réservation avec une date passée, vérifier le blocage.
- Tenter une réservation avec une date de départ avant ou égale à l’arrivée, vérifier le blocage.
- Tenter une réservation qui chevauche une réservation confirmée, vérifier le blocage.
- Vérifier le calcul : prix par nuit x nuits + frais de ménage.
- Simuler un paiement réussi après validation admin, vérifier `status=confirmed` et `payment_status=test_paid`.
- Simuler un paiement échoué, vérifier que la réservation reste en `pending_payment` avec `payment_status=test_failed`.
- Ouvrir le détail réservation locataire, vérifier le reçu fictif imprimable et la mention “Document fictif — aucune transaction réelle”.
- Depuis `/proprietaire/disponibilites`, définir un prix spécifique sur une nuit future, réserver cette nuit côté locataire et vérifier que le total utilise le prix override + frais de ménage.

## Propriétaire

- Connecté propriétaire validé, créer un logement et vérifier qu’il est créé en `pending`.
- Téléverser une image JPG/PNG/WEBP valide et vérifier son affichage ; refuser un format interdit ou supérieur à 5 Mo.
- Modifier son propre logement.
- Modifier un logement déjà publié : changer le prix, soumettre, vérifier le message “Vos modifications ont été soumises à validation” et contrôler que la fiche publique conserve l’ancien prix.
- Vérifier que `/proprietaire/logements` affiche le badge “Modification en attente”.
- Tenter de modifier directement une image d’un logement publié depuis les actions galerie, vérifier que le changement est refusé et renvoie vers le formulaire de modification.
- Tenter d’éditer l’URL d’un logement d’un autre propriétaire, vérifier le refus.
- Soumettre un logement à validation.
- Définir une plage indisponible dans `/proprietaire/disponibilites` et vérifier que les dates sont bloquées.
- Définir une plage disponible avec prix spécifique, vérifier l’aperçu des disponibilités à venir.
- Vérifier le calendrier mensuel : légende, navigation mois précédent/suivant, dates passées désactivées, dates réservées non éditables et prix override visible.
- Cliquer une date disponible dans le calendrier et vérifier le préremplissage début/fin du formulaire de plage.
- Modifier le texte alternatif d’une image, définir une image principale puis supprimer une image.
- Vérifier que seules les réservations de ses logements sont visibles.
- Filtrer les réservations reçues par logement, statut et dates.

## Admin

- Ouvrir `/admin/dashboard`, vérifier les statistiques globales.
- Ouvrir `/admin/utilisateurs`, `/admin/logements` et `/admin/reservations`, vérifier que l’accès admin fonctionne avec le compte de démonstration.
- Vérifier les compteurs “comptes à valider”, “logements à valider”, “réservations à valider”, “paiements en attente”, “messages non traités” et “avis à modérer”.
- Lister les utilisateurs, rechercher par email/nom, filtrer par rôle/statut et suspendre puis réactiver un compte.
- Approuver ou refuser un compte utilisateur depuis la liste et depuis la fiche détail.
- Ouvrir `/admin/utilisateurs/{id}` et vérifier les informations de compte, profil hôte ou réservations selon le rôle.
- Modifier un utilisateur depuis `/admin/utilisateurs/{id}/modifier` : prénom, nom, email, téléphone, rôle et statut.
- Vérifier qu’il est impossible de suspendre, rétrograder ou désactiver le dernier administrateur actif.
- Approuver ou rejeter un profil propriétaire et vérifier les compteurs de logements.
- Filtrer `/admin/logements` par statut/type/propriétaire/localisation/recherche.
- Ouvrir `/admin/logements/{id}`, vérifier images, équipements, propriétaire et actions.
- Modifier un logement depuis `/admin/logements/{id}/modifier`.
- Ouvrir `/admin/logements/modifications`, vérifier les demandes de modification propriétaires en attente.
- Ouvrir une demande de modification, comparer ancienne et nouvelle valeur, approuver puis vérifier que la fiche publique reflète la nouvelle valeur.
- Créer une seconde demande de modification, la refuser avec motif et vérifier que la fiche publique conserve les anciennes données.
- Publier, rejeter ou archiver un logement et vérifier que les logements non publiés restent invisibles sur le front.
- Mettre un logement publié en pause, vérifier qu’il disparaît de `/hebergements` et que sa fiche publique renvoie une 404.
- Vérifier que le propriétaire voit le badge “En pause” et ne peut pas réactiver lui-même le logement.
- Réactiver le logement depuis l’admin, vérifier qu’il redevient visible publiquement.
- Supprimer logiquement un logement depuis l’admin, vérifier qu’il passe en statut “Supprimé”, disparaît du front et conserve ses réservations/avis en historique admin.
- Filtrer `/admin/reservations` par statut, paiement, logement, propriétaire, locataire et période.
- Valider une réservation en attente de validation et vérifier qu’elle passe en attente de paiement.
- Modifier le statut d’une réservation, l’annuler et la marquer terminée.
- Ouvrir `/admin/reservations/{id}` et vérifier le détail complet : locataire, logement, dates, prix, paiement fictif de démonstration et statut.
- Filtrer `/admin/avis` par statut, note, logement et auteur.
- Publier ou rejeter un avis.
- Supprimer un avis depuis la modération.
- Créer un article de blog.
- Modifier, publier, dépublier puis supprimer un article de blog.
- Ouvrir `/admin/messages/{id}`, marquer un message comme lu, traité puis archivé.
- Filtrer les messages par contact/newsletter/RGPD, vérifier le badge “À traiter” sur une demande RGPD nouvelle.
- Consulter les journaux d’audit, filtrer par action/email/date et vérifier la présence des logs `user_registered_pending`, `user_approved`, `user_rejected`, `user_suspended`, `property_submitted`, `property_approved`, `property_paused`, `property_reactivated`, `property_deleted`, `property_change_requested`, `property_change_approved`, `property_change_rejected`, `booking_created_pending_admin`, `booking_validated_awaiting_payment`, `fake_payment_succeeded`, `booking_confirmed_after_payment`, `booking_completed_by_admin`, `review_approved`, `blog_published`, `message_processed`.
- Vérifier la pagination des utilisateurs, logements, messages et réservations admin.

## Avis

- Marquer une réservation passée en `completed`.
- Connecté locataire, déposer un avis depuis le détail de réservation.
- Vérifier que l’avis est `pending`.
- Publier l’avis depuis l’admin.
- Vérifier que l’avis publié apparaît sur la fiche logement.
- Vérifier que la note moyenne et chaque avis utilisent un affichage en étoiles avec texte accessible.
- Vérifier qu’un avis ne peut pas être créé sans réservation complétée.
- Vérifier que les avis de démonstration restent visibles dans `/admin/avis` avec le logement et le locataire fictif associés.

## Sécurité

- Soumettre un formulaire sans jeton CSRF valide, vérifier le blocage.
- Tester une entrée de type `' OR 1=1 --` dans la connexion, vérifier que l’injection SQL est neutralisée.
- Saisir `<script>alert(1)</script>` dans un message, vérifier que l’affichage est échappé.
- Vérifier qu’un propriétaire ne peut pas modifier un logement qui ne lui appartient pas.
- Vérifier qu’un locataire ne peut pas consulter la réservation d’un autre locataire.
- Demander `/mot-de-passe-oublie`, vérifier le message neutre, ouvrir le lien local de démonstration, changer le mot de passe puis vérifier que le token est supprimé.
- Refuser les cookies et vérifier que les placeholders GA/GTM ne sont pas chargés ; accepter et vérifier le push `cookie_consent_accept` dans `dataLayer`.
- Soumettre une newsletter avec consentement, vérifier le message succès et l’audit `newsletter_demo_subscribe`.
- Soumettre `/mes-donnees` pour une demande d’accès, vérifier l’audit `privacy_request_submit` et l’apparition dans `/admin/messages` avec badge RGPD.
- Vérifier qu’une suppression d’article, d’avis, d’image ou de logement demande confirmation.
- Ouvrir une URL inexistante, vérifier la page 404 personnalisée.
