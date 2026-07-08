# Manuel utilisateur provisoire

## Visiteur

Un visiteur peut consulter l’accueil, le concept, le catalogue, les fiches logements, le blog, les pages légales et le formulaire de contact. Pour réserver, il doit créer un compte locataire.

## Locataire

1. Créer un compte ou se connecter.
2. Parcourir `/hebergements`.
3. Choisir un logement, des dates et un nombre de voyageurs.
4. Lancer la réservation.
5. Simuler le paiement en succès ou en échec.
6. Consulter le sous-total, les frais de ménage et le total fictif dans `/locataire/reservations`.
7. Imprimer le reçu fictif depuis le détail de réservation si nécessaire pour la démonstration.
8. Après passage en réservation complétée, déposer un avis.
9. En cas d’oubli, utiliser `/mot-de-passe-oublie`; le lien est affiché localement pour la démonstration.

## Propriétaire

1. Créer un compte propriétaire.
2. Ajouter un logement depuis `/proprietaire/logements/ajouter`.
3. Compléter les informations obligatoires et les équipements.
4. Soumettre le logement à validation.
5. Gérer la galerie : texte alternatif, image principale, ajout/suppression d’image.
6. Gérer les disponibilités par plage et définir un prix spécifique optionnel.
7. Utiliser le calendrier mensuel pour visualiser dates disponibles, indisponibles, prix spécifiques et dates réservées.
8. Suivre les réservations reçues et filtrer par logement, statut ou période.

## Administrateur

1. Se connecter avec le compte admin.
2. Consulter les statistiques, les derniers logs, les derniers messages et les logements en attente.
3. Rechercher un utilisateur, ouvrir sa fiche, modifier ses informations, suspendre ou réactiver son compte.
4. Contrôler les propriétaires : validation du profil, statut du compte et nombre de logements.
5. Filtrer les logements, ouvrir le détail admin, vérifier les images/équipements, modifier la fiche puis publier, refuser, mettre en pause, réactiver ou supprimer logiquement.
6. Filtrer les réservations, consulter le détail complet, annuler ou marquer une réservation comme terminée.
7. Modérer les avis avec filtres par statut, note, logement ou auteur.
8. Créer, modifier, publier, dépublier ou supprimer les articles de blog.
9. Ouvrir les messages contact/newsletter/RGPD, les marquer comme lus, traités ou archivés.
10. Utiliser les filtres et la pagination des logs pour contrôler les actions sensibles.

Un logement mis en pause ou supprimé logiquement n’est plus visible dans le catalogue public. Les réservations, avis, images et logs restent conservés pour l’historique de la démonstration.

## Cookies et suivi

Le visiteur peut accepter ou refuser les cookies de mesure. Aucun identifiant GA4/GTM réel n’est activé par défaut ; la structure est prête pour une intégration future.

## Données personnelles

Le visiteur peut déposer une demande simulée depuis `/mes-donnees`. Les demandes sont stockées dans les messages admin avec un badge RGPD. Il ne s’agit pas d’un traitement juridique réel ni d’une suppression automatique.

## Newsletter

La newsletter fonctionne en mode démonstration : l’email est stocké localement avec consentement. Les variables Brevo sont documentées pour une future intégration, mais aucun email réel n’est envoyé par défaut.

## Smoke test

Les commandes de contrôle rapide sont documentées dans `docs/smoke-test.md`.

## Rappel académique

Projet étudiant fictif — aucun achat, paiement ou réservation réelle ne peut être effectué.
