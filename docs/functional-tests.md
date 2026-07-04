# Tests fonctionnels AtypikHouse

## Authentification

- Créer un compte locataire depuis `/inscription`, vérifier la redirection vers `/connexion`.
- Créer un compte propriétaire depuis `/inscription?role=owner`, vérifier la création du profil propriétaire.
- Se connecter avec `tenant@atypikhouse.test` / `Tenant123!`, vérifier l’accès à `/locataire/dashboard`.
- Essayer une connexion avec un mauvais mot de passe, vérifier le message d’erreur.
- Passer un utilisateur en `suspended` depuis l’admin, vérifier qu’il ne peut plus se connecter.

## Catalogue

- Ouvrir `/hebergements`, vérifier que seuls les logements `published` apparaissent.
- Filtrer par type `treehouse`, vérifier que seules les cabanes remontent.
- Filtrer par prix maximum, vérifier que les logements plus chers sont exclus.
- Filtrer par capacité, vérifier que les logements trop petits sont exclus.
- Filtrer par équipement et dates disponibles.
- Ouvrir une fiche logement, vérifier galerie, description, équipements, avis et formulaire de réservation.

## Réservation

- Connecté locataire, réserver des dates futures disponibles.
- Tenter une réservation avec une date passée, vérifier le blocage.
- Tenter une réservation avec une date de départ avant ou égale à l’arrivée, vérifier le blocage.
- Tenter une réservation qui chevauche une réservation confirmée, vérifier le blocage.
- Vérifier le calcul : prix par nuit x nuits + frais de ménage.
- Simuler un paiement réussi, vérifier `status=confirmed` et `payment_status=test_paid`.
- Simuler un paiement échoué, vérifier que la réservation reste non confirmée.
- Depuis `/proprietaire/disponibilites`, définir un prix spécifique sur une nuit future, réserver cette nuit côté locataire et vérifier que le total utilise le prix override + frais de ménage.

## Propriétaire

- Connecté propriétaire, créer un logement en brouillon.
- Téléverser une image JPG/PNG/WEBP valide et vérifier son affichage ; refuser un format interdit ou supérieur à 5 Mo.
- Modifier son propre logement.
- Tenter d’éditer l’URL d’un logement d’un autre propriétaire, vérifier le refus.
- Soumettre un logement à validation.
- Définir une plage indisponible dans `/proprietaire/disponibilites` et vérifier que les dates sont bloquées.
- Définir une plage disponible avec prix spécifique, vérifier l’aperçu des disponibilités à venir.
- Modifier le texte alternatif d’une image, définir une image principale puis supprimer une image.
- Vérifier que seules les réservations de ses logements sont visibles.
- Filtrer les réservations reçues par logement, statut et dates.

## Admin

- Ouvrir `/admin/dashboard`, vérifier les statistiques globales.
- Lister les utilisateurs et suspendre un compte.
- Modifier un utilisateur depuis `/admin/utilisateurs/{id}/modifier` : prénom, nom, email, téléphone, rôle et statut.
- Vérifier qu’il est impossible de suspendre, rétrograder ou désactiver le dernier administrateur actif.
- Approuver ou rejeter un profil propriétaire.
- Publier, rejeter ou archiver un logement.
- Modifier le statut d’une réservation.
- Ouvrir `/admin/reservations/{id}` et vérifier le détail complet : locataire, logement, dates, prix, paiement test et statut.
- Publier ou rejeter un avis.
- Supprimer un avis depuis la modération.
- Créer un article de blog.
- Modifier puis supprimer un article de blog.
- Lire et archiver un message de contact.
- Consulter les journaux d’audit.

## Avis

- Marquer une réservation passée en `completed`.
- Connecté locataire, déposer un avis depuis le détail de réservation.
- Vérifier que l’avis est `pending`.
- Publier l’avis depuis l’admin.
- Vérifier que l’avis publié apparaît sur la fiche logement.
- Vérifier qu’un avis ne peut pas être créé sans réservation complétée.

## Sécurité

- Soumettre un formulaire sans jeton CSRF valide, vérifier le blocage.
- Tester une entrée de type `' OR 1=1 --` dans la connexion, vérifier que l’injection SQL est neutralisée.
- Saisir `<script>alert(1)</script>` dans un message, vérifier que l’affichage est échappé.
- Vérifier qu’un propriétaire ne peut pas modifier un logement qui ne lui appartient pas.
- Vérifier qu’un locataire ne peut pas consulter la réservation d’un autre locataire.
- Demander `/mot-de-passe-oublie`, vérifier le message neutre, ouvrir le lien local de démonstration, changer le mot de passe puis vérifier que le token est supprimé.
- Refuser les cookies et vérifier que les placeholders GA/GTM ne sont pas chargés ; accepter et vérifier le push `cookie_consent_accept` dans `dataLayer`.
