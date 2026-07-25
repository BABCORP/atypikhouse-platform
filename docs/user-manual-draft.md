# Manuel utilisateur provisoire

## Visiteur

Un visiteur peut consulter l’accueil, le concept, le catalogue, les fiches logements, le blog, les pages légales et le formulaire de contact. Pour réserver, il doit créer un compte locataire actif immédiatement après inscription.

## Locataire

1. Créer un compte locataire ou se connecter avec un compte actif.
2. Parcourir `/hebergements`.
3. Utiliser la liste de destinations disponibles pour filtrer les logements publiés.
4. Ajouter ou retirer un logement des favoris avec le bouton cœur sur une carte ou une fiche.
5. Consulter ses favoris depuis `/locataire/favoris`.
6. Choisir un logement, des dates et un nombre de voyageurs.
7. Lancer la réservation, qui passe d’abord en attente de validation administrateur.
8. Une fois la réservation confirmée par l’admin, simuler le paiement en succès ou en échec.
9. Consulter le sous-total, les frais de ménage et le total fictif dans `/locataire/reservations`.
10. Imprimer le reçu fictif depuis le détail de réservation si nécessaire pour la démonstration.
11. Après passage en réservation complétée, déposer un avis.
12. En cas d’oubli, utiliser `/mot-de-passe-oublie`; le lien est affiché localement pour la démonstration.

Les fiches logements affichent une note moyenne en étoiles et le détail des avis publiés. Les favoris restent propres à chaque compte locataire.

## Propriétaire

1. Créer un compte propriétaire et attendre la validation administrateur.
2. Ajouter un logement depuis `/proprietaire/logements/ajouter` une fois le compte validé.
3. Compléter les informations obligatoires et les équipements.
4. Soumettre le logement à validation : il reste invisible publiquement tant que l’admin ne l’a pas publié.
5. Modifier un logement déjà publié depuis `/proprietaire/logements/{id}/modifier`. Les changements de texte, prix, équipements ou images sont envoyés en demande de validation et ne remplacent pas immédiatement la fiche publique.
6. Suivre le badge “Modification en attente” ou “Modification refusée” dans la liste des logements. En cas de refus, le motif affiché permet de préparer une nouvelle demande.
7. Gérer la galerie : texte alternatif, image principale, ajout/suppression d’image. Pour un logement publié ou mis en pause, ces changements passent par le formulaire de modification afin d’être validés par l’admin.
8. Gérer les disponibilités par plage et définir un prix spécifique optionnel.
9. Utiliser le calendrier mensuel pour visualiser dates disponibles, indisponibles, prix spécifiques et dates réservées.
10. Suivre les réservations reçues et filtrer par logement, statut ou période.

## Administrateur

1. Se connecter avec le compte admin.
2. Consulter les statistiques, les derniers logs, les derniers messages et les logements en attente.
3. Rechercher un utilisateur, ouvrir sa fiche, modifier ses informations, approuver, refuser, suspendre ou réactiver son compte.
4. Contrôler les propriétaires : validation du profil, statut du compte et nombre de logements.
5. Filtrer les logements, ouvrir le détail admin, vérifier les images/équipements, modifier la fiche puis publier, refuser, mettre en pause, réactiver ou supprimer logiquement.
6. Ouvrir “Modifications” pour comparer les valeurs publiées et les valeurs proposées par un propriétaire, puis approuver ou refuser la demande avec un motif.
7. Filtrer les réservations, consulter le détail complet, valider une demande de réservation pour déclencher le paiement fictif, l’annuler ou la marquer comme terminée après paiement.
8. Modérer les avis avec filtres par statut, note, logement ou auteur.
9. Créer, modifier, publier, dépublier ou supprimer les articles de blog.
10. Ouvrir les messages contact/newsletter/RGPD, les marquer comme lus, traités ou archivés.
11. Utiliser les filtres et la pagination des logs pour contrôler les actions sensibles.

Un logement mis en pause ou supprimé logiquement n’est plus visible dans le catalogue public. Les réservations, avis, images et logs restent conservés pour l’historique de la démonstration.

Une modification proposée par un propriétaire ne devient publique qu’après approbation administrateur. Tant que la demande est en attente ou refusée, les visiteurs continuent à voir la dernière version validée.

Une réservation créée par un locataire actif reste en attente côté admin avant tout paiement fictif. Le paiement de démonstration devient accessible uniquement après validation de la demande par l’administrateur.

## Cookies et suivi

Le visiteur peut accepter ou refuser les cookies de mesure. Aucun identifiant GA4/GTM réel n’est activé par défaut ; la structure est prête pour une intégration future.

## Données personnelles

Le visiteur peut déposer une demande simulée depuis `/mes-donnees`. Les demandes sont stockées dans les messages admin avec un badge RGPD. Il ne s’agit pas d’un traitement juridique réel ni d’une suppression automatique.

## Newsletter

La newsletter fonctionne en mode démonstration : l’email est stocké localement avec consentement. Les emails transactionnels du site peuvent être envoyés via Brevo SMTP lorsque les variables Render sont configurées.

## Smoke test

Les commandes de contrôle rapide sont documentées dans `docs/smoke-test.md`.

## Rappel académique

Projet étudiant fictif. Aucun achat, paiement ou réservation réelle ne peut être effectué. Les informations présentées sont utilisées dans le cadre d’une démonstration académique.
