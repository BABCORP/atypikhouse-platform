# Rapport de validation locale

Date de validation : 25 mai 2026  
Environnement : MAMP, PHP 8.3.30, MySQL 8.0.44

## Vérifications exécutées

- Lint PHP sur l’ensemble des fichiers `app/`, `config/`, `public/` et `routes/` : aucun défaut de syntaxe.
- Import réel de `database/schema.sql` et `database/seed.sql`.
- Contrôle du seed : 3 utilisateurs, 8 logements publiés, 10 images de démonstration et 2 réservations de démonstration.
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

## Remise à zéro

Après les tests mutables, le schéma et le seed ont été réimportés afin de restituer un environnement de démonstration propre.

## Limites de démonstration

- Les logements seedés utilisent les photographies de démonstration fournies avec la maquette ; un placeholder accessible reste prévu uniquement en absence d’image.
- Le paiement est exclusivement fictif et ne traite aucune donnée bancaire.
- Les identifiants GA4/GTM restent volontairement vides par défaut.
