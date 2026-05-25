# Rapport de validation locale

Date de validation : 25 mai 2026  
Environnement : MAMP, PHP 8.3.30, MySQL 8.0.44

## Vérifications exécutées

- Lint PHP sur l’ensemble des fichiers `app/`, `config/`, `public/` et `routes/` : aucun défaut de syntaxe.
- Import réel de `database/schema.sql` et `database/seed.sql`.
- Contrôle du seed : 3 utilisateurs, 8 logements publiés et 2 réservations de démonstration.
- Chargement HTTP des pages accueil, catalogue filtré, fiche logement, FAQ et connexion : réponses `200`.
- Connexion locataire avec le compte de démonstration et accès à son dashboard.
- Réservation fictive et paiement test réussi : passage de `pending_payment` à `confirmed` avec `payment_status=test_paid`.
- Blocage d’une réservation chevauchant une réservation confirmée.
- Connexion propriétaire, affichage du dashboard et formulaire de logement.
- Upload PNG propriétaire : stockage hors dossier public et restitution via endpoint média contrôlé.
- Connexion administrateur, affichage des statistiques, modération des avis et CRUD blog.
- Test CSRF sans jeton : requête refusée avec le statut HTTP `419`.
- Vérification visuelle mobile dans le navigateur intégré : hero lisible et menu navigation fonctionnel.

## Remise à zéro

Après les tests mutables, le schéma et le seed ont été réimportés afin de restituer un environnement de démonstration propre.

## Limites de démonstration

- Les logements seedés emploient un visuel de remplacement cohérent tant qu’aucune photo n’est téléversée par un propriétaire.
- Le paiement est exclusivement fictif et ne traite aucune donnée bancaire.
- Les identifiants GA4/GTM restent volontairement vides par défaut.
