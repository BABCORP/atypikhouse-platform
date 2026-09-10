<?php
$page = $page ?? '';
$demoNotice = config('academic_disclaimer');
?>

<article role="article" class="legal-page">
    <div class="legal-container">
        <h1><?= e($heading) ?></h1>
        <p class="legal-disclaimer"><?= e($demoNotice) ?></p>

        <?php if ($page === 'mentions-legales'): ?>
            <p>Conformément aux dispositions de la loi n° 2004-575 du 21 juin 2004 pour la Confiance dans l’Économie Numérique (LCEN), les présentes mentions légales sont portées à la connaissance des utilisateurs du site internet www.atypikhouse.fr.</p>

            <section class="legal-section">
                <h2>1. Éditeur du site</h2>
                <p>Le site internet www.atypikhouse.fr est édité par la société AtypikHouse, société à responsabilité limitée (SARL) au capital social de 10 000 euros.</p>
                <h3>Siège social</h3>
                <p>12 rue du Château, 60350 Pierrefonds, France</p>
                <h3>Coordonnées administratives</h3>
                <ul class="legal-list">
                    <li>RCS Compiègne : 923 847 561</li>
                    <li>SIREN : 923 847 561</li>
                    <li>SIRET : 923 847 561 00012</li>
                    <li>Code APE/NAF : 5520Z – Hébergement touristique et autre hébergement de courte durée</li>
                    <li>Numéro de TVA intracommunautaire : FR 61 923847561</li>
                </ul>
                <h3>Contacts</h3>
                <ul class="legal-list">
                    <li>Email : contact@atypikhouse.fr</li>
                    <li>Téléphone : 03 44 88 21 10</li>
                    <li>Formulaire de contact : www.atypikhouse.fr/contact</li>
                </ul>
                <h3>Représentant légal</h3>
                <p>Thomas Lemaire, Co-gérant</p>
            </section>

            <section class="legal-section">
                <h2>2. Directeur de la publication</h2>
                <p>Le directeur de la publication est Monsieur Thomas Lemaire, Co-gérant de la société AtypikHouse, joignable à l’adresse électronique suivante :</p>
                <ul class="legal-list">
                    <li>thomas.lemaire@atypikhouse.fr</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>3. Conception et développement</h2>
                <p>Le site www.atypikhouse.fr a été conçu et développé dans le cadre du projet AtypikHouse par l’équipe projet composée de :</p>
                <ul class="legal-list">
                    <li>Jade CASSUTO — Cheffe de projet</li>
                    <li>Ruxandra SERBAN — UX/UI Designer</li>
                    <li>Bianca BUMB — Développeur Web Full-Stack</li>
                </ul>
                <p>Le développement du site est réalisé entièrement sur mesure via Visual Studio Code à l’aide des technologies :</p>
                <ul class="legal-list">
                    <li>HTML5</li>
                    <li>CSS3</li>
                    <li>JavaScript (ES6+)</li>
                    <li>PHP 8.2+</li>
                    <li>MySQL 8.0+</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>4. Hébergement</h2>
                <p>Le site www.atypikhouse.fr est hébergé par Render, plateforme cloud opérée par Render Services, Inc. :</p>
                <p>525 Brannan Street, Suite 300, San Francisco, CA 94107, États-Unis</p>
                <h3>Détails techniques</h3>
                <ul class="legal-list">
                    <li>service web Docker déployé sur Render</li>
                    <li>base de données MySQL externe configurée par variables d’environnement</li>
                    <li>certificat HTTPS géré par la plateforme d’hébergement</li>
                    <li>déploiement automatisé depuis le dépôt GitHub du projet</li>
                </ul>
                <p>Render assure l’exploitation de l’infrastructure cloud utilisée pour la démonstration en ligne. La configuration applicative, les sauvegardes de base de données et les variables d’environnement restent sous la responsabilité de l’équipe projet.</p>
                <p>Pour toute question technique liée à l’hébergement :</p>
                <ul class="legal-list">
                    <li>legal@render.com</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>5. Propriété intellectuelle</h2>
                <p>L’ensemble des éléments composant le site www.atypikhouse.fr :</p>
                <ul class="legal-list">
                    <li>textes</li>
                    <li>graphismes</li>
                    <li>logotypes</li>
                    <li>icônes</li>
                    <li>photographies</li>
                    <li>vidéos</li>
                    <li>logiciels</li>
                    <li>bases de données</li>
                    <li>architecture du site</li>
                </ul>
                <p>sont la propriété exclusive d’AtypikHouse ou font l’objet d’une autorisation d’utilisation accordée à AtypikHouse par leurs titulaires respectifs.</p>
                <p>Toute reproduction, représentation, modification, publication ou adaptation de tout ou partie des éléments du site est interdite sans autorisation écrite préalable d’AtypikHouse.</p>
                <p>Toute exploitation non autorisée du site ou de l’un quelconque des éléments qu’il contient est susceptible de constituer une contrefaçon sanctionnée par les dispositions des articles L.335-2 et suivants du Code de la propriété intellectuelle.</p>
                <p>Les photographies d’annonces restent la propriété de leurs auteurs respectifs.</p>
            </section>

            <section class="legal-section">
                <h2>6. Contenu utilisateur et modération</h2>
                <p>Les utilisateurs sont seuls responsables des contenus qu’ils publient :</p>
                <ul class="legal-list">
                    <li>annonces</li>
                    <li>avis</li>
                    <li>messages</li>
                    <li>photographies</li>
                </ul>
                <p>AtypikHouse se réserve le droit de modérer, modifier ou supprimer tout contenu qui :</p>
                <ul class="legal-list">
                    <li>viole les présentes mentions légales ou les CGU/CGV</li>
                    <li>est illicite, frauduleux, diffamatoire ou obscène</li>
                    <li>porte atteinte aux droits d’autrui</li>
                    <li>contient du spam ou de la publicité non autorisée</li>
                    <li>est mensonger ou trompeur</li>
                </ul>
                <p>AtypikHouse s’engage à traiter les signalements dans les meilleurs délais afin de garantir la conformité des contenus publiés sur la plateforme.</p>
            </section>

            <section class="legal-section">
                <h2>7. Protection des données personnelles</h2>
                <p>AtypikHouse collecte et traite des données à caractère personnel dans le cadre du fonctionnement de la plateforme, conformément :</p>
                <ul class="legal-list">
                    <li>au RGPD</li>
                    <li>à la loi Informatique et Libertés</li>
                    <li>aux obligations relatives aux plateformes numériques</li>
                </ul>
                <h3>Données collectées</h3>
                <p>Les données collectées incluent notamment :</p>
                <ul class="legal-list">
                    <li>nom et prénom</li>
                    <li>adresse email</li>
                    <li>numéro de téléphone</li>
                    <li>adresse postale</li>
                    <li>données de réservation</li>
                    <li>données de paiement traitées uniquement par le prestataire de paiement sécurisé</li>
                    <li>données de connexion</li>
                    <li>cookies de session</li>
                    <li>historique de navigation</li>
                </ul>
                <h3>Finalités du traitement</h3>
                <p>Les données sont utilisées pour :</p>
                <ul class="legal-list">
                    <li>la gestion des comptes utilisateurs</li>
                    <li>les réservations</li>
                    <li>la facturation</li>
                    <li>les communications liées aux réservations</li>
                    <li>la communication commerciale avec consentement</li>
                    <li>la prévention des fraudes</li>
                    <li>l’amélioration du service</li>
                </ul>
                <h3>Base légale du traitement</h3>
                <p>Le traitement des données repose sur :</p>
                <ul class="legal-list">
                    <li>l’exécution du contrat</li>
                    <li>le consentement explicite de l’utilisateur</li>
                    <li>l’intérêt légitime d’AtypikHouse</li>
                    <li>les obligations légales et fiscales</li>
                </ul>
                <h3>Droits des utilisateurs</h3>
                <p>Conformément à la réglementation applicable, les utilisateurs disposent des droits suivants :</p>
                <ul class="legal-list">
                    <li>droit d’accès</li>
                    <li>droit de rectification</li>
                    <li>droit à l’effacement</li>
                    <li>droit à la limitation du traitement</li>
                    <li>droit à la portabilité</li>
                    <li>droit d’opposition</li>
                    <li>droit de ne pas faire l’objet d’une décision automatisée</li>
                </ul>
                <p>Ces droits peuvent être exercés à l’adresse :</p>
                <ul class="legal-list">
                    <li>contact@atypikhouse.fr</li>
                </ul>
                <p>ou par courrier postal au siège social de la société.</p>
                <p>Une réponse sera apportée dans un délai maximum de 30 jours.</p>
                <h3>Responsable du traitement</h3>
                <p>AtypikHouse est responsable du traitement des données personnelles collectées via la plateforme.</p>
                <h3>Délégué à la Protection des Données (DPO)</h3>
                <p>Pour toute question relative au traitement des données personnelles :</p>
                <ul class="legal-list">
                    <li>dpo@atypikhouse.fr</li>
                </ul>
                <h3>Réclamation CNIL</h3>
                <p>Les utilisateurs peuvent également adresser une réclamation auprès de la CNIL :</p>
                <ul class="legal-list">
                    <li>www.cnil.fr</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>8. Cookies et technologies de suivi</h2>
                <p>Le site www.atypikhouse.fr utilise des cookies afin :</p>
                <ul class="legal-list">
                    <li>d’améliorer l’expérience utilisateur</li>
                    <li>d’analyser le trafic</li>
                    <li>de proposer des fonctionnalités personnalisées</li>
                </ul>
                <h3>Types de cookies utilisés</h3>
                <p>Cookies strictement nécessaires :</p>
                <ul class="legal-list">
                    <li>cookies de session</li>
                    <li>cookies de sécurité</li>
                    <li>cookies de consentement</li>
                </ul>
                <p>Cookies analytiques :</p>
                <ul class="legal-list">
                    <li>Google Analytics (anonymisé)</li>
                </ul>
                <p>Cookies de personnalisation :</p>
                <ul class="legal-list">
                    <li>mémorisation des filtres de recherche</li>
                    <li>préférences utilisateur</li>
                </ul>
                <p>Cookies publicitaires :</p>
                <ul class="legal-list">
                    <li>remarketing publicitaire optionnel</li>
                </ul>
                <h3>Gestion des cookies</h3>
                <p>Conformément à la directive ePrivacy et aux recommandations de la CNIL, les cookies non nécessaires nécessitent le consentement préalable de l’utilisateur.</p>
                <p>L’utilisateur peut :</p>
                <ul class="legal-list">
                    <li>accepter ou refuser les cookies</li>
                    <li>modifier ses préférences</li>
                    <li>gérer les paramètres depuis son navigateur</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>9. Responsabilité</h2>
                <p>AtypikHouse s’efforce d’assurer l’exactitude des informations publiées sur le site mais ne peut garantir :</p>
                <ul class="legal-list">
                    <li>l’exactitude complète des contenus</li>
                    <li>l’absence d’erreurs</li>
                    <li>l’absence d’interruption du service</li>
                </ul>
                <h3>Limitation de responsabilité</h3>
                <p>AtypikHouse ne pourra être tenu responsable :</p>
                <ul class="legal-list">
                    <li>des interruptions temporaires du site</li>
                    <li>des pertes de données</li>
                    <li>des actions des utilisateurs tiers</li>
                    <li>des dommages indirects liés à l’utilisation du site</li>
                </ul>
                <h3>Responsabilité des utilisateurs</h3>
                <p>Les utilisateurs sont seuls responsables :</p>
                <ul class="legal-list">
                    <li>des contenus qu’ils publient</li>
                    <li>de leurs annonces</li>
                    <li>de leur utilisation de la plateforme</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>10. Liens hypertextes</h2>
                <p>Le site peut contenir des liens vers des sites tiers. AtypikHouse ne peut être tenu responsable du contenu de ces sites externes.</p>
                <p>La création de liens vers le site AtypikHouse est autorisée sous réserve :</p>
                <ul class="legal-list">
                    <li>d’un usage informatif</li>
                    <li>d’une identification claire du lien</li>
                    <li>de l’absence d’atteinte aux intérêts de la plateforme</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>11. Droit applicable et juridiction compétente</h2>
                <p>Les présentes mentions légales sont soumises au droit français.</p>
                <p>En cas de litige relatif à leur interprétation ou leur exécution, les parties chercheront une solution amiable avant toute procédure judiciaire.</p>
                <p>À défaut d’accord amiable, les tribunaux compétents du ressort de Compiègne seront seuls compétents.</p>
            </section>

            <p class="legal-updated">Dernière mise à jour : Avril 2026</p>

        <?php elseif ($page === 'cgu'): ?>
            <p>Les présentes Conditions Générales d'Utilisation (ci-après « CGU ») régissent l'accès et l'utilisation de la plateforme AtypikHouse accessible à l'adresse www.atypikhouse.fr (ci-après « la Plateforme »). Toute utilisation de la Plateforme implique l'acceptation pleine et entière des présentes CGU.</p>

            <section class="legal-section">
                <h2>Article 1 — Objet</h2>
                <p>La Plateforme AtypikHouse est une place de marché numérique (marketplace) permettant la mise en relation entre des propriétaires d'hébergements insolites (cabanes, yourtes, bulles, tiny houses, roulottes, cabanes flottantes) et des voyageurs souhaitant réserver ces hébergements pour des séjours touristiques.</p>
                <p>AtypikHouse agit en qualité d'intermédiaire de mise en relation et non en qualité de propriétaire ou d'exploitant direct des hébergements référencés sur la Plateforme. Chaque contrat de location est conclu directement entre le propriétaire et le locataire.</p>
            </section>

            <section class="legal-section">
                <h2>Article 2 — Accès à la Plateforme</h2>
                <p>La Plateforme est accessible gratuitement à tout utilisateur disposant d'un accès à internet. Certaines fonctionnalités (réservation, gestion des annonces, accès aux espaces privés) nécessitent la création d'un compte utilisateur.</p>
                <p>AtypikHouse se réserve le droit de suspendre ou d'interrompre l'accès à la Plateforme à tout moment, notamment pour des opérations de maintenance, sans que cela engage sa responsabilité.</p>
            </section>

            <section class="legal-section">
                <h2>Article 3 — Création de compte</h2>
                <p>Pour accéder aux fonctionnalités complètes de la Plateforme, l'utilisateur doit créer un compte en fournissant des informations exactes, complètes et à jour. Deux types de comptes sont disponibles :</p>
                <ul class="legal-list">
                    <li>Compte Locataire : permet de rechercher, consulter et réserver des hébergements, d'accéder à l'historique des réservations et de déposer des avis.</li>
                    <li>Compte Propriétaire : permet de publier et gérer des annonces d'hébergements, de gérer les disponibilités et de consulter les réservations reçues.</li>
                </ul>
                <p>L'utilisateur est responsable de la confidentialité de ses identifiants de connexion et s'engage à informer AtypikHouse sans délai de toute utilisation non autorisée de son compte.</p>
                <p>AtypikHouse se réserve le droit de suspendre ou de supprimer tout compte en cas de violation des présentes CGU, de fourniture d'informations inexactes ou de comportement frauduleux.</p>
            </section>

            <section class="legal-section">
                <h2>Article 4 — Obligations des utilisateurs</h2>
                <p>En utilisant la Plateforme, chaque utilisateur s'engage à :</p>
                <ul class="legal-list">
                    <li>Respecter les lois et réglementations applicables, notamment celles relatives à la location saisonnière (loi Le Meur du 19 novembre 2024), au RGPD et au Code de la consommation.</li>
                    <li>Ne pas publier de contenus illicites, frauduleux, diffamatoires, obscènes ou portant atteinte aux droits des tiers.</li>
                    <li>Ne pas utiliser la Plateforme à des fins commerciales non autorisées ou pour contourner le système de réservation d'AtypikHouse.</li>
                    <li>Ne pas tenter d'accéder de manière non autorisée aux systèmes informatiques d'AtypikHouse.</li>
                    <li>Fournir des informations exactes et à jour lors de la création de compte et lors de toute transaction.</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Article 5 — Obligations des propriétaires</h2>
                <p>Les propriétaires référençant leurs hébergements sur la Plateforme s'engagent à :</p>
                <ul class="legal-list">
                    <li>Respecter les obligations légales liées à la location saisonnière : déclaration préalable en mairie, respect des durées maximales de location autorisées, conformité aux normes de sécurité et d'accessibilité.</li>
                    <li>Fournir des descriptions exactes et complètes de leurs hébergements, notamment concernant les équipements, la capacité d'accueil, les tarifs et les disponibilités.</li>
                    <li>Respecter les réservations confirmées et accueillir les locataires dans les conditions décrites dans l'annonce.</li>
                    <li>Maintenir leurs hébergements en bon état d'entretien et de propreté.</li>
                    <li>Souscrire une assurance responsabilité civile couvrant l'activité de location saisonnière.</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Article 6 — Avis et évaluations</h2>
                <p>AtypikHouse propose un système d'avis permettant aux locataires d'évaluer leur séjour à l'issue de chaque réservation. Les avis doivent être honnêtes, objectifs et rédigés dans le respect des tiers. AtypikHouse se réserve le droit de modérer et de supprimer tout avis ne respectant pas ces principes ou les présentes CGU.</p>
                <p>Les avis publiés sur la Plateforme sont soumis à modération et n'engagent que leurs auteurs. AtypikHouse ne saurait être tenu responsable du contenu des avis publiés par les utilisateurs.</p>
            </section>

            <section class="legal-section">
                <h2>Article 7 — Propriété intellectuelle</h2>
                <p>Tous les contenus publiés par les propriétaires sur la Plateforme (textes descriptifs, photographies, visuels) restent la propriété de leur auteur. En publiant ces contenus, le propriétaire accorde à AtypikHouse une licence non exclusive, mondiale et gratuite d'utilisation aux fins de promotion de la Plateforme et des hébergements.</p>
            </section>

            <section class="legal-section">
                <h2>Article 8 — Responsabilité d'AtypikHouse</h2>
                <p>AtypikHouse agit en qualité d'intermédiaire de mise en relation et ne peut être tenu responsable des dommages résultant directement de l'exécution ou de l'inexécution des contrats conclus entre propriétaires et locataires.</p>
                <p>AtypikHouse ne garantit pas l'exactitude des informations publiées par les propriétaires dans leurs annonces. Il appartient à chaque utilisateur de vérifier les informations présentées avant toute réservation.</p>
            </section>

            <section class="legal-section">
                <h2>Article 9 — Modification des CGU</h2>
                <p>AtypikHouse se réserve le droit de modifier les présentes CGU à tout moment. Les utilisateurs seront informés de toute modification substantielle par email ou par notification sur la Plateforme. La poursuite de l'utilisation de la Plateforme après notification des modifications vaut acceptation des nouvelles CGU.</p>
            </section>

            <section class="legal-section">
                <h2>Article 10 — Droit applicable</h2>
                <p>Les présentes CGU sont soumises au droit français. En cas de litige relatif à leur interprétation ou à leur exécution, et à défaut de résolution amiable, les juridictions compétentes de Compiègne seront saisies.</p>
            </section>

            <p class="legal-updated">Dernière mise à jour : avril 2026.</p>

        <?php elseif ($page === 'cgv'): ?>
            <p class="legal-disclaimer legal-disclaimer--sale">Dans le cadre de cette démonstration académique, les conditions de vente sont présentées à titre fictif. Aucun paiement réel, aucune réservation réelle et aucune transaction commerciale réelle ne sont effectués sur ce site.</p>
            <p>Les présentes Conditions Générales de Vente (ci-après « CGV ») s'appliquent à toutes les transactions réalisées sur la plateforme AtypikHouse accessible à l'adresse www.atypikhouse.fr. Elles régissent les relations commerciales entre AtypikHouse, les propriétaires d'hébergements et les locataires.</p>

            <section class="legal-section">
                <h2>Article 1 — Champ d'application</h2>
                <p>Les présentes CGV s'appliquent à toutes les réservations d'hébergements insolites effectuées via la Plateforme AtypikHouse par des utilisateurs ayant créé un compte locataire. Elles constituent le socle de la relation commerciale entre AtypikHouse (en qualité d'intermédiaire de paiement) et les locataires, ainsi qu'entre les propriétaires et les locataires pour l'exécution du séjour.</p>
            </section>

            <section class="legal-section">
                <h2>Article 2 — Description du service de réservation</h2>
                <p>AtypikHouse met à disposition des locataires un système de réservation en ligne permettant de sélectionner un hébergement, de choisir les dates de séjour, de calculer le prix total du séjour (incluant les éventuels frais de service), et de procéder au paiement sécurisé en ligne.</p>
                <p>La réservation est considérée comme confirmée dès réception par le locataire d'un email de confirmation envoyé automatiquement par la Plateforme. Cet email récapitule les informations essentielles du séjour : hébergement, dates, prix total, conditions d'annulation et coordonnées du propriétaire.</p>
            </section>

            <section class="legal-section">
                <h2>Article 3 — Prix et frais de service</h2>
                <p>Les prix affichés sur les fiches hébergements sont exprimés en euros toutes taxes comprises (TTC) et correspondent au tarif à la nuitée fixé par le propriétaire. Le prix total de la réservation est calculé automatiquement par la Plateforme sur la base du nombre de nuits sélectionnées.</p>
                <p>AtypikHouse perçoit des frais de service sur chaque réservation, calculés en pourcentage du montant total de la réservation. Ces frais sont clairement affichés et détaillés lors du processus de réservation, avant toute validation du paiement par le locataire.</p>
                <p>Le prix total affiché avant validation inclut le prix de la location, les frais de service AtypikHouse et toute taxe applicable. Aucun frais supplémentaire ne peut être ajouté après la validation du paiement par le locataire.</p>
            </section>

            <section class="legal-section">
                <h2>Article 4 — Paiement en ligne</h2>
                <p>Le paiement des réservations s'effectue exclusivement en ligne via la Plateforme, au moment de la confirmation de la réservation. AtypikHouse utilise un prestataire de paiement sécurisé certifié PCI-DSS afin de garantir la sécurité des transactions.</p>
                <p>Les moyens de paiement acceptés sont les suivants : carte bancaire (Visa, Mastercard, American Express), PayPal. AtypikHouse ne stocke aucune donnée bancaire sur ses serveurs.</p>
                <p>En cas de refus de paiement par l'établissement bancaire, la réservation est automatiquement annulée et AtypikHouse ne peut être tenu responsable de ce refus.</p>
            </section>

            <section class="legal-section">
                <h2>Article 5 — Conditions d'annulation</h2>
                <p>Les conditions d'annulation applicables à chaque réservation sont définies par le propriétaire de l'hébergement et clairement affichées sur la fiche hébergement avant la réservation. AtypikHouse propose trois politiques d'annulation standard :</p>
                <ul class="legal-list">
                    <li>Annulation flexible : remboursement intégral si annulation effectuée au moins 48 heures avant le début du séjour.</li>
                    <li>Annulation modérée : remboursement intégral si annulation effectuée au moins 7 jours avant le début du séjour, remboursement à 50 % passé ce délai.</li>
                    <li>Annulation stricte : aucun remboursement sauf annulation effectuée dans les 48 heures suivant la confirmation de la réservation et au moins 14 jours avant le début du séjour.</li>
                </ul>
                <p>Les frais de service AtypikHouse ne sont pas remboursés en cas d'annulation, sauf disposition contraire expressément indiquée lors de la réservation.</p>
            </section>

            <section class="legal-section">
                <h2>Article 6 — Obligations du locataire</h2>
                <p>Le locataire s'engage à :</p>
                <ul class="legal-list">
                    <li>Utiliser l'hébergement conformément à sa destination et aux règles établies par le propriétaire.</li>
                    <li>Respecter le nombre maximum d'occupants indiqué sur la fiche hébergement.</li>
                    <li>Laisser l'hébergement dans l'état dans lequel il l'a trouvé à son arrivée.</li>
                    <li>Respecter le règlement intérieur de l'hébergement et les propriétés voisines.</li>
                    <li>Signaler sans délai tout dommage causé à l'hébergement au propriétaire et à AtypikHouse.</li>
                </ul>
            </section>

            <section class="legal-section">
                <h2>Article 7 — Responsabilité du propriétaire</h2>
                <p>Le propriétaire est seul responsable de la conformité de son hébergement aux normes légales et réglementaires applicables (sécurité, accessibilité, déclaration en mairie). Il garantit au locataire la jouissance paisible de l'hébergement pendant la durée du séjour et s'engage à mettre à disposition le bien dans l'état décrit dans l'annonce.</p>
            </section>

            <section class="legal-section">
                <h2>Article 8 — Responsabilité d'AtypikHouse</h2>
                <p>AtypikHouse intervient uniquement en qualité d'intermédiaire entre propriétaires et locataires. AtypikHouse ne peut être tenu responsable des dommages résultant directement de l'exécution du séjour, de la non-conformité de l'hébergement à sa description, ou de tout litige entre propriétaire et locataire.</p>
                <p>En cas de litige, AtypikHouse s'engage à mettre en place une procédure de médiation entre les parties et peut, le cas échéant, intervenir comme tiers facilitateur sans toutefois avoir l'obligation de trancher le litige.</p>
            </section>

            <section class="legal-section">
                <h2>Article 9 — Données personnelles et paiement</h2>
                <p>Les données personnelles collectées lors du processus de réservation et de paiement sont traitées conformément à la politique de confidentialité d'AtypikHouse et au RGPD. Elles sont utilisées exclusivement aux fins de traitement de la réservation, de communication avec les parties et de prévention de la fraude.</p>
                <p>Les données de paiement sont transmises directement au prestataire de paiement sécurisé et ne sont jamais stockées par AtypikHouse.</p>
            </section>

            <section class="legal-section">
                <h2>Article 10 — Droit de rétractation</h2>
                <p>Conformément à l'article L221-28 du Code de la consommation, le droit de rétractation de 14 jours applicable aux contrats conclus à distance ne s'applique pas aux contrats de prestation de services d'hébergement pour une date ou une période déterminée. La réservation d'un hébergement sur AtypikHouse n'est donc pas soumise au droit de rétractation légal. Les conditions d'annulation définies à l'article 5 des présentes CGV s'appliquent en lieu et place.</p>
            </section>

            <section class="legal-section">
                <h2>Article 11 — Médiation et règlement des litiges</h2>
                <p>Conformément aux dispositions du Code de la consommation relatives au règlement amiable des litiges, AtypikHouse adhère à un service de médiation de la consommation. En cas de litige non résolu dans le cadre d'une réclamation directe auprès d'AtypikHouse, le locataire peut recourir gratuitement à ce service de médiation.</p>
                <p>La Commission européenne met également à disposition une plateforme de règlement en ligne des litiges accessible à l'adresse : https://ec.europa.eu/consumers/odr.</p>
            </section>

            <section class="legal-section">
                <h2>Article 12 — Droit applicable</h2>
                <p>Les présentes CGV sont soumises au droit français. En cas de litige relatif à leur interprétation ou à leur exécution, et à défaut de résolution amiable, les juridictions compétentes de Compiègne seront saisies.</p>
            </section>

            <p class="legal-updated">Dernière mise à jour : avril 2026.</p>

        <?php elseif ($page === 'politique-confidentialite'): ?>
            <section class="legal-section">
                <h2>Données collectées</h2>
                <p>Le projet peut stocker localement : identité de compte, email, téléphone facultatif, profil propriétaire, logements saisis, disponibilités, réservations fictives, avis, messages de contact, demandes RGPD simulées, inscriptions newsletter de démonstration et journaux d’audit.</p>
                <h2>Finalités</h2>
                <p>Ces données servent à démontrer l’authentification, la séparation des rôles, la réservation de démonstration, la modération administrateur, la gestion propriétaire et la traçabilité technique. Aucune exploitation commerciale réelle n’est effectuée.</p>
                <h2>Durée de conservation fictive</h2>
                <p>En MVP académique, les données restent dans la base locale jusqu’à réimport du seed, suppression manuelle ou réinitialisation de l’environnement. Il ne s’agit pas d’une politique de conservation applicable à une entreprise réelle.</p>
                <h2>Droits RGPD simulés</h2>
                <p>Une demande d’accès, rectification, suppression ou opposition peut être déposée via <a href="<?= url('/mes-donnees') ?>">le formulaire Mes données</a>. La demande est visible en administration, sans suppression automatique afin de préserver le contexte académique.</p>
            </section>

        <?php else: ?>
            <section class="legal-section">
                <h2>Cookies et mesure d’audience</h2>
                <p>Le site utilise des cookies de session nécessaires au fonctionnement. Les emplacements GA4/GTM existent sous forme de placeholders et ne chargent aucun identifiant réel par défaut. Le bandeau cookies permet d’accepter ou refuser les scripts de mesure éventuels.</p>
                <h2>Gestion du consentement</h2>
                <p>L’utilisateur peut accepter ou refuser les cookies de mesure depuis le bandeau prévu à cet effet. Aucun suivi publicitaire réel n’est activé dans cette démonstration académique.</p>
            </section>
        <?php endif; ?>
    </div>
</article>
