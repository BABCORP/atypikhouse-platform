# Guide de soutenance AtypikHouse

Ce document sert à expliquer le site à l’oral avec des mots simples. Il est pensé pour une personne qui n’a pas de connaissances techniques en création de site internet.

## 1. Pitch court

AtypikHouse est un projet étudiant fictif de marketplace d’hébergements insolites.

Le site permet à des voyageurs de rechercher un logement, de consulter une fiche, de faire une réservation de démonstration, d’ajouter des favoris et de laisser un avis. Il permet aussi à des propriétaires de proposer des logements. L’administrateur garde le contrôle de la plateforme : il valide les comptes, les logements, les réservations, les avis, les messages et les contenus importants.

Phrase simple à dire :

> AtypikHouse reproduit le fonctionnement d’une plateforme de réservation comme un MVP académique. Le projet ne réalise aucun vrai paiement et aucune vraie réservation, mais il montre tout le parcours utilisateur de manière réaliste.

## 2. Ce que veut dire MVP

MVP signifie “Minimum Viable Product”.

Dans ce projet, cela veut dire que le site contient les fonctions principales nécessaires pour démontrer une marketplace :

- un catalogue de logements ;
- des comptes utilisateurs ;
- un espace locataire ;
- un espace propriétaire ;
- un back-office administrateur ;
- un workflow de validation ;
- des réservations et paiements fictifs ;
- des avis, favoris, messages et pages légales.

Phrase simple à dire :

> Le but n’était pas de créer une plateforme commerciale complète, mais une version démontrable, cohérente et sécurisée des principales fonctionnalités.

## 3. Les trois rôles du site

Le site fonctionne avec trois types d’utilisateurs.

### Visiteur

Le visiteur peut consulter les pages publiques :

- accueil ;
- concept ;
- catalogue ;
- fiches logements ;
- blog ;
- FAQ ;
- contact ;
- pages légales.

Il ne peut pas réserver tant qu’il n’est pas connecté avec un compte locataire actif.

### Locataire / voyageur

Le locataire peut :

- se connecter ;
- consulter ses réservations ;
- réserver un logement publié ;
- faire un paiement fictif ;
- ajouter des logements en favoris ;
- laisser un avis après un séjour terminé ;
- modifier son profil.

### Propriétaire / hôte

Le propriétaire peut :

- se connecter à son espace ;
- ajouter un logement ;
- compléter les informations du logement ;
- ajouter une image principale et d’autres images ;
- renseigner les équipements ;
- gérer les disponibilités ;
- suivre les réservations reçues ;
- proposer des modifications sur un logement publié.

Important :

Un propriétaire ne peut pas publier directement un logement. L’administrateur doit valider.

### Administrateur

L’administrateur est le centre de contrôle du site. Il peut :

- valider ou refuser les comptes ;
- gérer les utilisateurs ;
- valider, refuser, mettre en pause, réactiver ou supprimer les logements ;
- valider les modifications proposées par les propriétaires ;
- suivre les réservations ;
- confirmer, annuler ou terminer une réservation ;
- modérer les avis ;
- gérer les articles de blog ;
- traiter les messages ;
- consulter les logs d’audit.

Phrase simple à dire :

> L’administrateur sert à sécuriser la plateforme : rien d’important ne devient public sans contrôle.

## 4. Parcours principal à présenter

Voici le scénario idéal à montrer pendant la soutenance.

### Étape 1 : montrer le site public

À montrer :

- page d’accueil ;
- menu ;
- footer ;
- page concept ;
- catalogue des hébergements ;
- fiche logement.

À expliquer :

> La partie publique sert à donner confiance au visiteur. On peut découvrir le concept, chercher un logement, filtrer les résultats et consulter une fiche détaillée.

Fonctions à citer :

- design responsive ;
- hover sur les cartes et boutons ;
- page active dans le menu ;
- recherche par destination ;
- cartes logement avec image, prix, note, favoris ;
- fiche logement avec galerie, équipements, calendrier et avis.

### Étape 2 : montrer la recherche de logement

À montrer :

- `/hebergements` ;
- champ destination ;
- filtres ;
- carte logement ;
- bouton coeur favori.

À expliquer :

> La recherche aide l’utilisateur à trouver un séjour selon la destination, le type de logement, la capacité, le prix, les équipements ou les dates.

Point important :

Les destinations proposées viennent uniquement des logements publiés. Un logement en attente, refusé, en pause ou supprimé n’apparaît pas publiquement.

### Étape 3 : montrer la fiche logement

À montrer :

- titre ;
- images ;
- prix ;
- description ;
- équipements ;
- étoiles d’avis ;
- calendrier ;
- formulaire de réservation.

À expliquer :

> La fiche logement rassemble les informations utiles pour aider le voyageur à se projeter. Les avis affichent des étoiles, les équipements sont lisibles et le calendrier indique les dates disponibles ou bloquées.

### Étape 4 : montrer le compte locataire

Identifiants :

- email : `locataire@atypikhouse.fr`
- mot de passe : `Tenant123!`

À montrer :

- dashboard locataire ;
- favoris ;
- réservations ;
- détail de réservation ;
- avis.

À expliquer :

> Le locataire dispose d’un espace personnel. Il peut retrouver ses réservations, ses favoris, son profil et ses avis.

### Étape 5 : montrer une réservation

À expliquer :

> Une réservation passe d’abord en attente de validation administrateur. Cela permet de montrer un workflow contrôlé, même si aucun vrai paiement n’est effectué.

Workflow :

1. Le locataire choisit un logement publié.
2. Il choisit les dates et le nombre de voyageurs.
3. Le site vérifie les dates, la capacité et les conflits.
4. La réservation est créée en attente.
5. L’admin confirme.
6. Le locataire peut ensuite accéder au paiement fictif.

Point à dire clairement :

> Le paiement est fictif. Aucun numéro de carte bancaire n’est demandé.

### Étape 6 : montrer le compte propriétaire

Identifiants :

- email : `proprietaire@atypikhouse.fr`
- mot de passe : `Owner123!`

À montrer :

- dashboard propriétaire ;
- liste des logements ;
- formulaire ajouter un logement ;
- gestion des images ;
- équipements avec option “Autre” ;
- disponibilités ;
- réservations reçues.

À expliquer :

> Le propriétaire peut proposer un logement, mais il ne peut pas le publier directement. Le logement est envoyé à l’administrateur pour validation.

Points importants :

- image principale séparée des autres images ;
- upload sécurisé ;
- texte alternatif pour les images ;
- équipements classiques et équipements personnalisés ;
- statut visible : publié, en attente, refusé, en pause ;
- modification d’un logement publié soumise à validation.

Phrase à dire :

> Si un propriétaire modifie le prix ou les photos d’un logement déjà publié, le public continue de voir l’ancienne version tant que l’administrateur n’a pas validé la modification.

### Étape 7 : montrer le back-office admin

Identifiants :

- email : `admin@atypikhouse.fr`
- mot de passe : `Admin123!`

À montrer :

- dashboard admin ;
- utilisateurs ;
- propriétaires ;
- logements ;
- modifications de logements ;
- réservations ;
- avis ;
- messages ;
- logs.

À expliquer :

> Le back-office permet à l’administrateur de piloter toute la plateforme. C’est la partie la plus importante pour prouver que le site n’est pas seulement une vitrine.

Fonctions importantes :

- statistiques globales ;
- comptes à valider ;
- logements à valider ;
- modifications à valider ;
- réservations à valider avant paiement fictif ;
- paiements fictifs en attente ;
- messages non traités ;
- avis à modérer ;
- logs d’audit.

Phrase à dire :

> Les logs d’audit servent à garder une trace des actions sensibles, par exemple la validation d’un logement ou la modification du statut d’une réservation.

## 5. Ce qui a été développé sur le site

### Front-office

Nous avons travaillé la partie visible du site :

- accueil ;
- concept ;
- catalogue ;
- fiche logement ;
- blog ;
- FAQ ;
- devenir hôte ;
- contact ;
- pages légales ;
- footer ;
- responsive mobile ;
- animations de survol ;
- menu avec page active ;
- textes plus professionnels.

### Catalogue et logements

Fonctionnalités ajoutées ou améliorées :

- filtres de recherche ;
- liste de destinations disponibles ;
- cartes logement plus vivantes ;
- affichage des notes avec étoiles ;
- favoris avec bouton coeur ;
- calendrier sur fiche logement ;
- visibilité uniquement des logements publiés ;
- invisibilité des logements en attente, refusés, en pause ou supprimés.

### Favoris

Fonctionnement :

- un locataire connecté peut ajouter un logement en favori ;
- il peut le retirer ;
- un utilisateur non connecté est redirigé vers la connexion ;
- les favoris sont propres à chaque utilisateur.

À expliquer simplement :

> Le coeur permet au locataire de garder de côté les logements qui l’intéressent.

### Réservations

Fonctionnement :

- réservation uniquement pour les locataires actifs ;
- propriétaires et administrateurs ne peuvent pas réserver ;
- contrôle des dates ;
- contrôle de la capacité ;
- réservation en attente de validation ;
- confirmation par l’admin ;
- paiement fictif après confirmation.

### Paiement fictif

À expliquer :

> Le paiement est volontairement fictif, car le projet est académique. Il sert à montrer le parcours utilisateur sans traiter d’argent réel.

Fonctions :

- scénario de paiement validé ;
- scénario de paiement refusé ;
- statut de paiement ;
- reçu fictif imprimable.

### Avis

Fonctions :

- avis liés aux logements ;
- affichage en étoiles ;
- note moyenne ;
- modération administrateur ;
- seuls les avis publiés sont visibles publiquement.

### Espace propriétaire

Fonctions :

- création de logement ;
- formulaire détaillé ;
- images principales et secondaires ;
- équipements personnalisés ;
- disponibilités ;
- prix spécifique par date ;
- suivi des réservations ;
- modification soumise à validation.

### Back-office admin

Fonctions :

- dashboard complet ;
- gestion utilisateurs ;
- validation comptes ;
- gestion propriétaires ;
- validation logements ;
- pause, réactivation et suppression logique ;
- validation des modifications propriétaires ;
- gestion réservations ;
- gestion paiements fictifs ;
- modération avis ;
- gestion blog ;
- traitement messages ;
- logs d’audit.

### Pages légales et conformité

Pages présentes :

- mentions légales ;
- CGU ;
- CGV ;
- politique de confidentialité ;
- cookies ;
- demande relative aux données personnelles.

À expliquer :

> Les pages légales sont présentes pour rendre le projet plus réaliste, mais elles indiquent clairement que le site reste un projet étudiant fictif.

## 6. Sécurité expliquée simplement

Tu peux expliquer la sécurité sans entrer dans le code.

### Mots de passe

Les mots de passe ne sont pas stockés en clair. Ils sont hashés.

Phrase simple :

> Même dans la base de données, on ne voit pas directement le mot de passe.

### Requêtes préparées

Le site utilise PDO et des requêtes préparées.

Phrase simple :

> Cela protège la base de données contre des injections SQL, c’est-à-dire des tentatives de manipulation par formulaire.

### CSRF

Les formulaires sensibles sont protégés par un jeton CSRF.

Phrase simple :

> Cela évite qu’une action sensible soit lancée depuis une autre page sans autorisation.

### Rôles

Chaque espace est séparé :

- locataire ;
- propriétaire ;
- administrateur.

Phrase simple :

> Un locataire ne peut pas accéder à l’administration, et un propriétaire ne peut pas modifier les logements d’un autre propriétaire.

### Upload d’images

Les images sont contrôlées :

- formats acceptés ;
- taille limitée ;
- nom de fichier sécurisé ;
- pas de fichier dangereux.

### Suppression logique

Quand l’admin supprime un logement, il n’est pas effacé brutalement.

Phrase simple :

> Le logement disparaît du catalogue public, mais son historique reste conservé pour ne pas casser les réservations, les avis ou les logs.

## 7. Architecture expliquée simplement

Le projet utilise une architecture MVC.

### MVC veut dire quoi ?

MVC signifie :

- Model : communique avec la base de données ;
- View : affiche les pages ;
- Controller : reçoit les actions de l’utilisateur et coordonne le reste.

Phrase simple à dire :

> Cette organisation permet de séparer les responsabilités : la base de données, l’affichage et la logique ne sont pas mélangés.

### Technologies utilisées

- PHP pour le backend ;
- MySQL pour la base de données ;
- PDO pour communiquer avec MySQL ;
- HTML pour la structure des pages ;
- CSS pour le design ;
- JavaScript vanilla pour les petites interactions ;
- aucune dépendance lourde comme Laravel, Symfony, React ou WordPress.

Phrase simple :

> Le site est fait sur mesure, sans CMS ni framework lourd, ce qui permet de montrer que la logique du projet a été construite spécifiquement.

## 8. Base de données expliquée simplement

La base de données contient notamment :

- utilisateurs ;
- profils propriétaires ;
- logements ;
- images ;
- équipements ;
- disponibilités ;
- réservations ;
- paiements fictifs ;
- avis ;
- articles de blog ;
- messages ;
- favoris ;
- logs d’audit ;
- demandes de modification logement.

Phrase simple :

> La base de données sert à conserver tout ce qui fait vivre la plateforme : comptes, logements, réservations, avis et actions administrateur.

## 9. Points de démonstration à ne pas oublier

Pendant la soutenance, tu peux montrer dans cet ordre :

1. Accueil.
2. Catalogue.
3. Fiche logement.
4. Connexion locataire.
5. Favoris.
6. Réservation.
7. Connexion propriétaire.
8. Ajout ou modification logement.
9. Connexion admin.
10. Validation logement ou réservation.
11. Logs d’audit.
12. Pages légales.

Cette démonstration raconte une histoire complète :

> Un propriétaire propose un logement, l’administrateur le valide, un locataire le réserve, l’administrateur suit la réservation, puis le locataire peut laisser un avis.

## 10. Phrases utiles pour répondre au jury

### Pourquoi le paiement est fictif ?

> Parce que le projet est académique. L’objectif est de montrer le parcours de réservation sans traiter d’argent réel ni de données bancaires.

### Pourquoi l’admin doit valider les logements ?

> Pour éviter qu’un contenu incomplet ou non conforme soit publié directement. C’est une sécurité éditoriale et fonctionnelle.

### Pourquoi les comptes passent par une validation admin ?

> Cela permet de contrôler qui peut utiliser les fonctionnalités sensibles, comme réserver ou publier un logement.

### Pourquoi les propriétaires ne peuvent pas publier directement ?

> Parce qu’une marketplace doit garantir un catalogue fiable. L’admin vérifie le contenu, les images, le prix, les équipements et la cohérence générale.

### Pourquoi les modifications d’un logement publié ne changent pas tout de suite ?

> Pour éviter qu’un propriétaire modifie une fiche visible publiquement sans contrôle. La version publique reste stable jusqu’à validation.

### Pourquoi les logements supprimés ne sont pas effacés complètement ?

> Pour conserver l’historique des réservations, des avis et des actions administrateur. C’est plus sûr pour l’intégrité des données.

### Pourquoi il y a des logs ?

> Les logs permettent de garder une trace des actions importantes : validation, refus, suspension, changement de statut, etc.

### Pourquoi le site affiche un disclaimer ?

> Pour rappeler clairement que le projet est fictif et qu’aucun achat, paiement ou réservation réelle ne peut être effectué.

## 11. Limites assumées du projet

Il faut être honnête sur les limites.

Le projet ne contient pas :

- vrai paiement bancaire ;
- vrai envoi d’email ;
- vraie intégration Stripe, Brevo ou Google Analytics active ;
- application mobile ;
- messagerie instantanée ;
- calendrier professionnel très avancé ;
- suppression RGPD automatique complète.

Phrase simple :

> Ces limites sont assumées car le projet est un MVP académique. La structure est prévue pour évoluer, mais l’objectif de soutenance est de montrer un parcours complet, sécurisé et cohérent.

## 12. Checklist avant soutenance

Avant de présenter :

- vérifier que le site démarre ;
- vérifier que la base de données est importée ;
- tester la connexion admin ;
- tester la connexion propriétaire ;
- tester la connexion locataire ;
- ouvrir le catalogue ;
- ouvrir une fiche logement ;
- vérifier qu’un logement publié s’affiche ;
- vérifier que le back-office admin fonctionne ;
- garder les identifiants ouverts dans `docs/identifiants-acces.md`.

Identifiants de démonstration :

- Admin : `admin@atypikhouse.fr` / `Admin123!`
- Propriétaire : `proprietaire@atypikhouse.fr` / `Owner123!`
- Locataire : `locataire@atypikhouse.fr` / `Tenant123!`

## 13. Conclusion possible à l’oral

Tu peux terminer avec cette phrase :

> AtypikHouse est un MVP complet de marketplace fictive. Le site présente un parcours public, un espace locataire, un espace propriétaire et un back-office administrateur. Les fonctionnalités principales sont démontrables : recherche, favoris, réservation, paiement fictif, avis, validation des logements, modération et suivi des actions. Le projet reste académique, mais il a été pensé comme une base professionnelle, sécurisée et évolutive.
