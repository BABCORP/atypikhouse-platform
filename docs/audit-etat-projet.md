# Audit etat du projet AtypikHouse

Date d'audit : 2026-07-08  
Branche auditee : `codex/atypikhouse-mvp`  
Perimetre : audit statique du code, schema SQL, seed, documentation, lint PHP/JS et controles base locale.  
Modification fonctionnelle pendant l'audit : aucune.

## Resume executif

Le projet est dans un etat avance et presentable pour une soutenance MVP, sous reserve de corriger quelques angles morts de workflow et de preparer une base propre avant la demo. Les fondations attendues par le cahier des charges sont presentes : front-office, catalogue, fiches logements, comptes locataire/proprietaire/admin, reservation, paiement fictif, avis moderes, back-office, pages legales/RGPD, SEO de base, cookies, analytics placeholders, documentation.

Les risques principaux ne sont pas une absence de fonctionnalite majeure, mais des details de robustesse :

- certains formulaires peuvent afficher un succes meme si l'action n'a rien modifie ;
- les slugs de logements/articles ne sont pas proteges contre les doublons cote formulaire ;
- quelques comportements doivent etre testes manuellement en navigateur, surtout responsive et workflows complets ;
- la base locale contient deja un logement en pause, donc une reimportation propre est recommandee avant soutenance.

Verdict : projet globalement presentable, mais pas encore "blindé". Les corrections prioritaires doivent stabiliser les retours utilisateur, les slugs uniques, les workflows admin/proprietaire et le jeu de donnees de demo.

## Scores estimes

| Axe | Score |
| --- | ---: |
| Global | 17/20 |
| Front-office | 17/20 |
| Espace proprietaire | 16/20 |
| Back-office admin | 18/20 |
| Securite | 17/20 |
| Soutenance | 17/20 |

## Validations executees

| Commande | Resultat |
| --- | --- |
| `find app config public routes -name '*.php' -exec /Applications/MAMP/bin/php/php8.3.30/bin/php -l '{}' \;` | OK, aucune erreur de syntaxe PHP detectee |
| `node --check public/assets/js/main.js` | OK, aucune erreur de syntaxe JS detectee |
| Requetes MySQL de comptage local | OK, donnees presentes |

Etat base locale observe :

| Donnee | Total |
| --- | ---: |
| Utilisateurs | 11 |
| Logements | 13 |
| Logements publies | 12 |
| Images logement | 16 |
| Reservations | 27 |
| Avis | 26 |
| Articles blog | 2 |
| Messages contact | 1 |
| Logs audit | 36 |

Statuts logements locaux :

| Statut | Total |
| --- | ---: |
| `published` | 12 |
| `paused` | 1 |

Aucun doublon de slug ni logement sans image n'a ete detecte dans la base locale au moment de l'audit.

## Audit architecture

| Element | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Structure MVC custom | ✅ FAIT | `app/Controllers`, `app/Models`, `app/Views`, `app/Core`, `routes/web.php` | Architecture coherente avec le CDC | P4 | Conserver cette structure |
| Routing | ✅ FAIT | `routes/web.php` couvre public, auth, locataire, proprietaire, admin | Nombreuses routes utiles et lisibles | P4 | Ne pas changer le style de routes |
| Configuration | ✅ FAIT | `config/app.php`, `config/database.php`, `.env.example` | Compatible MAMP via variables DB | P4 | Documenter la config finale de demo |
| Assets | ✅ FAIT | `public/assets/css/main.css`, `public/assets/js/main.js`, `public/assets/img/properties` | Assets locaux, pas de framework lourd | P4 | Continuer a optimiser les images |
| Base SQL | ✅ FAIT | `database/schema.sql`, `database/seed.sql` | Schema complet et seed riche | P3 | Reimport propre avant soutenance |
| Documentation | 🟡 PARTIEL | `README.md`, `docs/functional-tests.md`, `docs/user-manual-draft.md`, `docs/validation-report.md` | Bonne base, mais doit suivre les dernieres corrections | P2 | Mettre a jour apres les derniers correctifs |
| Fichiers temporaires | ✅ FAIT | `git status --short` propre avant audit | Pas de `.swp` ou `.DS_Store` detecte dans l'etat Git | P4 | Maintenir `.gitignore` |

## Audit front-office public

| Page / fonctionnalite | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Accueil `/` | ✅ FAIT | `PublicController::home`, `app/Views/public/home.php` | Hero, featured, newsletter, disclaimer | P4 | Verifier rendu final en navigateur |
| Concept `/concept` | ✅ FAIT | `PublicController::concept`, `app/Views/public/concept.php` | Page retravaillee editorialement | P4 | Verifier desktop/mobile |
| Catalogue `/hebergements` | ✅ FAIT | `PublicController::catalogue`, `Property::published` | Recherche/filtres presents | P3 | Ajouter validation douce si dates inversees |
| Fiche logement `/hebergements/{slug}` | ✅ FAIT | `PublicController::property`, `Property::findBySlug` | Fiche uniquement publiee, calendrier, avis | P3 | Test manuel responsive/calendrier |
| Blog `/blog`, `/blog/{slug}` | ✅ FAIT | `BlogPost::published`, `findBySlug` | Blog public publie seulement | P3 | Ajouter controle slug unique en admin |
| FAQ `/faq` | ✅ FAIT | route + vue `public/faq.php` | Contenu demo present | P4 | Aucun blocage |
| Devenir hote `/devenir-hote` | ✅ FAIT | `PublicController::host` | Page existe | P4 | Aucun blocage |
| Contact `/contact` | ✅ FAIT | `sendContact`, CSRF, validation email/consentement | Stockage local | P4 | Aucun blocage |
| Pages legales | ✅ FAIT | `public/legal.php`, routes legales | Disclaimer et RGPD demo presents | P4 | Relire contenu pour soutenance |
| Connexion/inscription | ✅ FAIT | `AuthController`, vues `auth/*` | Role tenant/owner, hash password | P4 | Aucun blocage |
| Mot de passe oublie | ✅ FAIT | routes reset + table `password_resets` | Lien affiche en demo locale | P4 | Bien expliquer en soutenance |

## Audit catalogue et fiches logements

| Fonctionnalite | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Seuls les logements publies sont publics | ✅ FAIT | `Property::published`, `Property::findBySlug` filtrent `status = "published"` | Les statuts pending/rejected/paused/deleted sont exclus | P4 | Conserver |
| Recherche et filtres | ✅ FAIT | `Property::published($filters)` | Destination, type, capacite, prix, equipement, dates, tri | P3 | Valider explicitement `end_date > start_date` cote catalogue |
| Images et alt | ✅ FAIT | `property_images`, `public/property.php`, `property-form.php` | Images locales et alt affiches/editables | P4 | Controler coherence visuelle finale |
| Calendrier public | 🟡 PARTIEL | `data-availability-calendar` dans `public/property.php`, JS `main.js` | Present et propre, mais verification mobile manuelle necessaire | P2 | Test navigateur desktop/mobile |
| Prix override | ✅ FAIT | `Booking::calculatePrice`, `nightlyPrices` | Les overrides sont pris en compte par nuit | P4 | Ajouter un cas de demo visible |
| Logements similaires | 🟡 PARTIEL | `related => $model->published(['type' => ...], 3)` | Peut inclure le logement courant dans les resultats | P3 | Exclure l'ID courant |
| Avis publics | ✅ FAIT | `Review::forProperty` filtre `status = "published"` | 26 avis en base locale | P4 | Aucun blocage |

## Audit authentification et roles

| Fonctionnalite | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Hash mot de passe | ✅ FAIT | `User::create`, `password_hash(PASSWORD_DEFAULT)` | Conforme | P4 | Aucun blocage |
| Connexion securisee | ✅ FAIT | `password_verify`, `session_regenerate_id` | Suspended bloque | P4 | Aucun blocage |
| Rate limiting login | ✅ FAIT | `AuthController::authenticate` | Limite session 5 tentatives | P4 | Suffisant MVP |
| RBAC | ✅ FAIT | `Auth::requireRole` | Admin peut aussi passer les roles tenant/owner, conforme a l'ancien besoin admin global | P3 | Documenter ce choix |
| CSRF | ✅ FAIT | `verify_csrf()` sur POST sensibles | Present sur auth, booking, owner, admin | P4 | Aucun blocage |
| Reset password demo | ✅ FAIT | `sendResetLink`, `resetPassword` | Pas d'email reel, lien flash demo | P4 | Expliquer en demo |

## Audit espace locataire

| Fonctionnalite | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Dashboard locataire | ✅ FAIT | `TenantController::dashboard` | Affiche reservations recentes | P4 | Aucun blocage |
| Historique reservations | ✅ FAIT | `Booking::tenantBookings` | Filtre par `tenant_id` | P4 | Aucun blocage |
| Detail reservation | ✅ FAIT | `Booking::findForTenant` | Ownership respecte | P4 | Aucun blocage |
| Paiement fictif | ✅ FAIT | `BookingController::simulatePayment` | Success/failure sans vrai paiement | P4 | Aucun blocage |
| Recu/facture fictive | ✅ FAIT | Vue `dashboard/booking-detail.php`, print JS/CSS | Present d'apres code existant | P3 | Test impression navigateur |
| Avis apres sejour | ✅ FAIT | `Review::canReview`, `BookingController::review` | En attente moderation | P4 | Aucun blocage |
| Profil | ✅ FAIT | `TenantController::updateProfile`, `updatePassword` | CSRF + validation password | P4 | Aucun blocage |

## Audit espace proprietaire

| Fonctionnalite | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Dashboard proprietaire | ✅ FAIT | `OwnerController::dashboard` | Stats via properties/bookings | P4 | Aucun blocage |
| Creation logement | ✅ FAIT | `OwnerController::storeProperty`, `Property::create` | Cree en `draft` | P4 | Aucun blocage |
| Soumission validation | ✅ FAIT | `Property::submit` draft/rejected vers pending | Invisible public avant admin | P4 | Aucun blocage |
| Edition logement | 🟡 PARTIEL | `editProperty` charge tout logement possede, `Property::update` modifie seulement draft/rejected/pending | Un proprietaire peut ouvrir directement l'URL d'edition d'un logement published/paused ; l'update ne persiste pas mais affiche un succes | P1 | Bloquer l'acces edition si statut non editable et afficher message clair |
| Slug unique a la creation | 🟡 PARTIEL | `Property::create` slugifie le titre | Un titre duplique peut provoquer erreur SQL unique | P1 | Generer slug unique ou afficher erreur formulaire |
| Galerie images | ✅ FAIT | `addGalleryImage`, `updateImageAlt`, `setMainImage`, `deleteImage` | Ownership controle, placeholder si besoin | P4 | Aucun blocage |
| Upload image | ✅ FAIT | `Upload::propertyImage` | MIME JPG/PNG/WEBP, max 5 Mo, nom aleatoire | P4 | Aucun blocage |
| Disponibilites par plage | ✅ FAIT | `storeAvailability`, max 90 jours, dates futures | Propre MVP | P4 | Aucun blocage |
| Calendrier owner | ✅ FAIT | Vue `dashboard/availability.php`, JS calendar | Present | P3 | Test manuel mobile |
| Reservations owner | ✅ FAIT | `Booking::ownerBookings` filtre `owner_id` | Filtres property/status/date | P4 | Aucun blocage |
| Statut pause admin visible | ✅ FAIT | `status_label('paused')`, tableaux proprietaire | Visible comme statut | P3 | Ajouter message pedagogique si besoin |

## Audit back-office admin

| Fonctionnalite | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Dashboard admin | ✅ FAIT | `AdminController::dashboard`, `AdminStats` | Stats + derniers logs/reservations/logements | P4 | Aucun blocage |
| Utilisateurs liste/filtres | ✅ FAIT | `AdminController::users`, `User::paginated` | Recherche, role, statut, pagination | P4 | Aucun blocage |
| Edition utilisateur | ✅ FAIT | `editUser`, `updateUser` | Role/statut/email unique, logs | P4 | Aucun blocage |
| Protection dernier admin actif | ✅ FAIT | `User::isLastActiveAdmin` utilise dans admin | Empêche suspension/retrogradation | P4 | Aucun blocage |
| Proprietaires | 🟡 PARTIEL | `AdminController::owners`, `updateOwnerStatus` | Profils visibles/moderables ; suspension via users plutot que page owners | P3 | Ajouter action suspendre/reactiver directe si souhaite |
| Logements liste/filtres | ✅ FAIT | `AdminController::properties`, `Property::paginatedForAdmin` | Statuts, type, owner, location, search | P4 | Aucun blocage |
| Detail logement admin | ✅ FAIT | `propertyDetail` + vue `admin-property-detail.php` | Voir infos, images, bookings count | P4 | Aucun blocage |
| Modifier logement admin | ✅ FAIT | `updateProperty`, `Property::updateAdmin` | Validation serveur | P4 | Aucun blocage |
| Valider/refuser logement | ✅ FAIT | `approveProperty`, `rejectProperty` | Messages flash + logs | P4 | Aucun blocage |
| Pause/reactivation/suppression logique | ✅ FAIT | `pauseProperty`, `reactivateProperty`, `deleteProperty` | Redirections admin corrigees, public exclu | P4 | Aucun blocage |
| Reservations admin | ✅ FAIT | `bookings`, `bookingDetail`, `updateBookingStatus` | Filtres et detail complet | P4 | Aucun blocage |
| Avis admin | ✅ FAIT | `reviews`, `updateReviewStatus`, `deleteReview` | Moderation publish/reject/delete | P4 | Aucun blocage |
| Blog admin | 🟡 PARTIEL | `blog`, `storeBlog`, `updateBlog`, `publishBlog` | Fonctionnel, mais slug unique non gere proprement au formulaire | P2 | Gerer doublon slug avec message |
| Messages admin | ✅ FAIT | `messages`, `messageDetail`, status read/processed/archived | Contact/newsletter/RGPD differencies | P4 | Aucun blocage |
| Logs audit | ✅ FAIT | `logs` avec filtres action/user/date/entity | Read-only | P4 | Aucun blocage |
| Messages flash redirection admin logement | ✅ FAIT | `quickPropertyStatus(..., '/admin/logements/...')` | Plus de retour public 404 apres action admin | P4 | Aucun blocage |

## Workflow marketplace complet

| Etape | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| 1. Proprietaire cree un compte | ✅ FAIT | `AuthController::store`, role owner + profile | OK | P4 | Aucun blocage |
| 2. Proprietaire ajoute logement | ✅ FAIT | `OwnerController::storeProperty` | Cree brouillon | P4 | Aucun blocage |
| 3. Soumission en attente | ✅ FAIT | `Property::submit` | Draft/rejected -> pending | P4 | Aucun blocage |
| 4. Admin valide | ✅ FAIT | `approveProperty` | Status published | P4 | Aucun blocage |
| 5. Apparition publique | ✅ FAIT | `Property::published` | Published only | P4 | Aucun blocage |
| 6. Locataire reserve | ✅ FAIT | `BookingController::start` | Dates/capacite/dispo validees | P4 | Aucun blocage |
| 7. Paiement fictif | ✅ FAIT | `simulatePayment` | Transaction fictive | P4 | Aucun blocage |
| 8. Reservation visible espaces | ✅ FAIT | tenant/owner/admin booking queries | Ownership respecte | P4 | Aucun blocage |
| 9. Avis apres sejour | ✅ FAIT | `Review::canReview` | Booking completed requis | P4 | Aucun blocage |
| 10. Admin modere avis | ✅ FAIT | `updateReviewStatus` | Published/rejected | P4 | Aucun blocage |
| 11. Pause admin | ✅ FAIT | status `paused` | Exclu public | P4 | Aucun blocage |
| 12. Reactivation admin | ✅ FAIT | status `published` depuis paused | OK | P4 | Aucun blocage |
| 13. Suppression logique admin | ✅ FAIT | status `deleted` | Historique conserve | P4 | Aucun blocage |

## Audit securite

| Controle | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| PDO / requetes preparees | ✅ FAIT | Modeles utilisent `prepare/execute` | Quelques requetes query sans input utilisateur | P4 | OK |
| Echappement HTML | ✅ FAIT | helper `e()`, vues principales | Bonne pratique generale | P4 | Continuer |
| CSRF | ✅ FAIT | `verify_csrf()` sur POST | OK | P4 | Aucun blocage |
| RBAC admin | ✅ FAIT | `Auth::requireRole('admin')` routes admin | OK | P4 | Aucun blocage |
| Ownership tenant | ✅ FAIT | `findForTenant`, `tenantBookings` | OK | P4 | Aucun blocage |
| Ownership owner | ✅ FAIT | `findOwned`, joins owner | Direct edit statut non editable a corriger | P1 | Bloquer edition published/paused |
| Upload securise | ✅ FAIT | `Upload::propertyImage` | MIME, taille, nom aleatoire | P4 | Aucun blocage |
| Stack traces | 🟡 PARTIEL | `public/index.php`, `Database.php` selon `APP_DEBUG` | En demo locale debug peut exposer erreurs | P2 | Mettre `APP_DEBUG=false` pour soutenance si possible |
| Suppression dangereuse | ✅ FAIT | Admin logement = status deleted, owner delete seulement draft/rejected | Reservations conservees | P4 | Aucun blocage |
| Audit logs | ✅ FAIT | helper `audit`, actions sensibles nombreuses | Pas de detail/user_agent | P3 | Ajouter champ details optionnel si besoin |

## Audit base de donnees

| Element | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Tables CDC principales | ✅ FAIT | `schema.sql` contient users, owner_profiles, properties, images, amenities, availabilities, bookings, payments, reviews, blog, messages, logs, resets | Complet | P4 | Aucun blocage |
| Statuts logement | ✅ FAIT | ENUM inclut draft/pending/published/rejected/archived/paused/deleted | Cycle admin complet | P4 | Aucun blocage |
| Donnees demo | ✅ FAIT | Seed + DB locale riche | 13 logements locaux, 1 paused | P2 | Reimport propre avant soutenance |
| Images | ✅ FAIT | 16 images locales en DB | Pas de logement sans image detecte | P4 | Aucun blocage |
| Avis | ✅ FAIT | 26 avis locaux | Rendu realiste | P4 | Aucun blocage |
| Doublons slugs existants | ✅ FAIT | Requete doublons vide | Pas de doublon local | P4 | Prevenir doublons futurs |
| Contraintes FK | ✅ FAIT | `schema.sql` avec foreign keys | Bon historique | P4 | Aucun blocage |

## Audit UX / design

| Zone | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Header/footer | ✅ FAIT | `layouts/main.php`, Instagram footer | Navigation complete, disclaimer visible | P4 | Aucun blocage |
| Homepage | 🟡 PARTIEL | Vue `home.php`, CSS recent | Visuel probablement propre mais test navigateur requis | P2 | Revue finale desktop/mobile |
| Concept | 🟡 PARTIEL | Vue `concept.php`, CSS recent | Corrige mais demande validation visuelle humaine | P2 | Revue finale avec capture |
| Catalogue | 🟡 PARTIEL | `catalogue.php` + search hero | Formulaire refondu | P2 | Revue responsive |
| Fiche logement | 🟡 PARTIEL | `property.php` + calendrier | Fonctionnelle mais calendrier/responsive a tester | P2 | Test mobile |
| Dashboards | ✅ FAIT | Vues dashboard nombreuses | Tables, badges, filtres | P3 | Polissage seulement |
| Back-office | ✅ FAIT | Vues admin dediees | Demo-ready | P3 | Test scenario complet |
| Accessibilite | 🟡 PARTIEL | Labels, aria, focus CSS, alt | Bonne base, pas d'audit WCAG complet | P2 | Tester clavier et mobile |

## Audit SEO / legal / contenu

| Element | Statut | Preuve | Probleme | Priorite | Correction recommandee |
| --- | --- | --- | --- | --- | --- |
| Meta/canonical/OG | ✅ FAIT | `layouts/main.php`, `PublicController` | Present sur pages importantes | P4 | Aucun blocage |
| JSON-LD | ✅ FAIT | Property LodgingBusiness, BlogPosting | Mention fictive | P4 | Aucun blocage |
| Sitemap/robots | 🟡 PARTIEL | `public/sitemap.xml`, `robots.txt` | URLs locales `127.0.0.1:8010` | P3 | OK demo locale, changer si deploiement |
| Legal/RGPD | ✅ FAIT | `public/legal.php`, `/mes-donnees` | Workflow RGPD simule | P4 | Aucun blocage |
| Cookies/analytics | ✅ FAIT | banner + `main.js`, GA/GTM placeholders | Aucun ID reel par defaut | P4 | Aucun blocage |
| Brevo/newsletter | ✅ FAIT | `NewsletterService`, README | Mode demo local par defaut | P4 | Aucun email reel |

## Bugs critiques et risques

| Bug / risque | Impact | Fichier / route | Correction recommandee |
| --- | --- | --- | --- |
| Edition directe d'un logement proprietaire non editable | UX trompeuse : formulaire accessible, succes affiche alors que l'UPDATE ne modifie rien | `/proprietaire/logements/{id}/modifier`, `OwnerController`, `Property::update` | Bloquer `published/paused/deleted` dans `editProperty` et verifier rowCount/update result |
| Slug logement duplique | Peut provoquer une erreur SQL si un proprietaire cree un titre deja existant | `Property::create`, `Property::update` | Generer slug unique ou afficher erreur claire |
| Slug article blog duplique | Peut provoquer erreur SQL admin | `BlogPost`, `AdminController::storeBlog/updateBlog` | Valider unicite slug avant insert/update |
| Base locale deja modifiee | Demo moins previsible si un logement reste paused ou si donnees de test trainent | MySQL local | Reimporter `schema.sql` + `seed.sql` avant soutenance |
| Tests responsive non automatises | Risque visuel en soutenance | Pages publiques et dashboards | Faire revue navigateur desktop/tablette/mobile |

## Taches restantes

### Priorite 1 - Critique avant soutenance

- Bloquer l'edition proprietaire des logements `published`, `paused`, `deleted` ou afficher une erreur claire.
- Gerer proprement les slugs uniques pour logements et blog.
- Reimporter une base propre et verifier les comptes demo.
- Jouer le workflow complet proprietaire -> admin -> locataire -> avis -> moderation.

### Priorite 2 - Important

- Revue responsive manuelle des pages `/`, `/concept`, `/hebergements`, fiches logements, dashboards.
- Verifier les calendriers public/owner/admin en largeur mobile.
- Mettre `APP_DEBUG=false` pour une demo plus propre si l'environnement le permet.
- Mettre a jour la documentation apres les derniers correctifs.

### Priorite 3 - Amelioration

- Exclure le logement courant des logements similaires.
- Ajouter action suspendre/reactiver directement depuis `/admin/proprietaires`.
- Ajouter details plus riches dans `audit_logs`.
- Valider dates inversees dans les filtres catalogue.

### Priorite 4 - Bonus

- Export PDF facture fictive.
- Tests automatises HTTP plus complets.
- Mini script de reset demo.
- Audit accessibilite WCAG manuel plus pousse.

## Plan de correction progressif

1. Stabilisation securite/routes : slugs uniques, editions non autorisees, messages flash coherents.
2. Workflow marketplace : rejouer creation hote, validation admin, reservation locataire, paiement fictif, avis, moderation.
3. Back-office : tester actions utilisateurs, logements, reservations, avis, blog, messages et logs.
4. UX/design : corriger uniquement les ecrans qui cassent visuellement en demo.
5. Documentation/tests : README, manuel, functional-tests, validation-report.
6. Polish soutenance : reset base, verifier comptes demo, preparer scenario de presentation.

## Verdict

Le projet est presentable, oui. Il couvre largement le cahier des charges MVP : marketplace fictive, roles, reservation, paiement fictif de démonstration, back-office, proprietaires, avis, legal/RGPD, SEO et documentation.

Ce qui bloque vraiment une soutenance fluide n'est pas une fonctionnalite majeure absente, mais la robustesse des cas limites et la preparation de demo. Il faut prioriser les slugs, les editions proprietaire non autorisees, la base propre et les tests manuels du parcours complet.
