<section class="page-hero compact">
    <p class="eyebrow">Accessibilité</p>
    <h1>Plan du site</h1>
    <p>Retrouvez les principales pages du site AtypikHouse depuis une vue simple et structurée.</p>
</section>

<section class="section site-map-page" aria-labelledby="site-map-heading">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Navigation</p>
            <h2 id="site-map-heading">Toutes les zones importantes</h2>
        </div>
        <p class="muted">Cette page est destinée aux visiteurs. Elle ne remplace pas le sitemap technique utilisé par les moteurs de recherche.</p>
    </div>

    <div class="site-map-grid">
        <article role="article" class="panel">
            <h2>Pages principales</h2>
            <ul class="link-list">
                <li><a href="<?= url('/') ?>">Accueil</a></li>
                <li><a href="<?= url('/concept') ?>">Le concept</a></li>
                <li><a href="<?= url('/hebergements') ?>">Hébergements</a></li>
                <li><a href="<?= url('/devenir-hote') ?>">Devenir hôte</a></li>
                <li><a href="<?= url('/blog') ?>">Blog</a></li>
                <li><a href="<?= url('/faq') ?>">FAQ</a></li>
                <li><a href="<?= url('/contact') ?>">Contact</a></li>
            </ul>
        </article>

        <article role="article" class="panel">
            <h2>Compte utilisateur</h2>
            <ul class="link-list">
                <li><a href="<?= url('/connexion') ?>">Connexion</a></li>
                <li><a href="<?= url('/inscription') ?>">Créer un compte</a></li>
                <li><a href="<?= url('/mot-de-passe-oublie') ?>">Mot de passe oublié</a></li>
                <li><a href="<?= url('/locataire/dashboard') ?>">Espace locataire</a></li>
                <li><a href="<?= url('/proprietaire/dashboard') ?>">Espace propriétaire</a></li>
            </ul>
        </article>

        <article role="article" class="panel">
            <h2>Informations légales</h2>
            <ul class="link-list">
                <li><a href="<?= url('/mentions-legales') ?>">Mentions légales</a></li>
                <li><a href="<?= url('/cgu') ?>">Conditions générales d’utilisation</a></li>
                <li><a href="<?= url('/cgv') ?>">Conditions générales de vente</a></li>
                <li><a href="<?= url('/politique-confidentialite') ?>">Politique de confidentialité</a></li>
                <li><a href="<?= url('/cookies') ?>">Gestion des cookies</a></li>
                <li><a href="<?= url('/mes-donnees') ?>">Demande relative aux données personnelles</a></li>
            </ul>
        </article>

        <article role="article" class="panel">
            <h2>Back-office de démonstration</h2>
            <ul class="link-list">
                <li><a href="<?= url('/admin/dashboard') ?>">Tableau de bord administrateur</a></li>
                <li><a href="<?= url('/admin/utilisateurs') ?>">Gestion des utilisateurs</a></li>
                <li><a href="<?= url('/admin/logements') ?>">Gestion des logements</a></li>
                <li><a href="<?= url('/admin/reservations') ?>">Gestion des réservations</a></li>
                <li><a href="<?= url('/admin/messages') ?>">Messages de contact</a></li>
            </ul>
            <p class="form-help">Ces liens nécessitent un compte administrateur connecté.</p>
        </article>
    </div>
</section>
