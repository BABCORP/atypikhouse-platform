<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">Nature · évasion · authenticité</p>
        <h1>Vivez l’expérience d’un hébergement insolite</h1>
        <p class="hero-lead">Découvrez des lieux singuliers au coeur de la nature et composez un séjour responsable qui sort du cadre.</p>
        <div class="actions">
            <a class="button" href="<?= url('/hebergements') ?>" data-track="cta_click">Découvrir les hébergements</a>
            <a class="button outline-light" href="<?= url('/devenir-hote') ?>" data-track="cta_click">Devenir hôte</a>
        </div>
    </div>
    <form class="search-panel hero-search" method="get" action="<?= url('/hebergements') ?>" data-track="search_submit">
        <label>Destination<input name="destination" placeholder="Où voulez-vous partir ?"></label>
        <label>Arrivée<input name="start_date" type="date"></label>
        <label>Départ<input name="end_date" type="date"></label>
        <label>Voyageurs<input name="capacity" type="number" min="1" value="2"></label>
        <button class="button" type="submit">Rechercher</button>
    </form>
</section>

<section class="section">
    <div class="section-heading">
        <div><p class="eyebrow">Sélection</p><h2>Nos séjours coups de coeur</h2></div>
        <a class="text-link" href="<?= url('/hebergements') ?>">Voir tous les logements</a>
    </div>
    <div class="grid cards">
        <?php foreach ($properties as $property): require __DIR__ . '/_property-card.php'; endforeach; ?>
    </div>
</section>

<section class="section band">
    <p class="eyebrow">Simple et responsable</p>
    <h2>Réserver une parenthèse en trois étapes</h2>
    <div class="features">
        <article><h3>1. Recherchez</h3><p>Filtrez par destination, type de logement, capacité et budget.</p></article>
        <article><h3>2. Réservez fictivement</h3><p>Choisissez vos dates, vérifiez le prix et lancez un paiement test.</p></article>
        <article><h3>3. Voyagez autrement</h3><p>Projetez-vous dans des séjours nature, simples et mémorables.</p></article>
    </div>
</section>

<section class="section home-flow">
    <div class="home-flow-main">
        <article class="home-editorial">
            <p class="eyebrow">Tourisme responsable</p>
            <h2>Des hébergements insolites pensés pour ralentir</h2>
            <p>AtypikHouse valorise les séjours nature, l’éco-score, les équipements utiles et les propriétaires engagés. Les données restent fictives, mais le parcours reproduit un vrai modèle de marketplace.</p>
            <a class="text-link" href="<?= url('/concept') ?>" data-track="cta_click">Comprendre le concept</a>
        </article>
        <form class="panel newsletter-form" method="post" action="<?= url('/newsletter') ?>" data-track="newsletter_submit">
            <?= csrf_field() ?>
            <p class="eyebrow">Inspiration</p>
            <h2>Recevoir l’inspiration séjour nature</h2>
            <label>Email<input required type="email" name="email" placeholder="vous@example.test"></label>
            <label class="checkbox"><input required type="checkbox" name="newsletter_consent" value="1"> J’accepte l’inscription newsletter fictive sans envoi réel.</label>
            <button class="button full" type="submit">S’inscrire</button>
        </form>
    </div>
    <div class="home-flow-secondary">
        <article class="home-proof">
            <p class="eyebrow">Confiance</p>
            <h2>Des expériences pensées pour se reconnecter</h2>
            <blockquote>“Très belle expérience fictive, l’ambiance nature est réussie.” <small>- Tania, voyageuse démo</small></blockquote>
        </article>
        <article class="home-categories">
            <p class="eyebrow">Explorer</p>
            <h2>Catégories insolites</h2>
            <div class="pill-list">
                <a href="<?= url('/hebergements?type=treehouse') ?>">Cabanes</a>
                <a href="<?= url('/hebergements?type=yurt') ?>">Yourtes</a>
                <a href="<?= url('/hebergements?type=floating_cabin') ?>">Cabanes flottantes</a>
                <a href="<?= url('/hebergements?type=tiny_house') ?>">Tiny houses</a>
                <a href="<?= url('/hebergements?type=dome') ?>">Dômes</a>
            </div>
        </article>
    </div>
    <div class="home-final-cta">
        <div>
            <p class="eyebrow">Une question ?</p>
            <h2>Préparez votre prochaine escapade insolite</h2>
        </div>
        <a class="button secondary" href="<?= url('/contact') ?>" data-track="cta_click">Nous contacter</a>
    </div>
</section>
