<section class="hero">
    <div class="hero-content">
        <p class="eyebrow">Nature · évasion · authenticité</p>
        <h1>Vivez l’expérience d’un hébergement insolite</h1>
        <p>Découvrez une sélection fictive de cabanes perchées, yourtes, tiny houses et dômes responsables pour préparer un séjour qui sort du cadre.</p>
        <div class="actions">
            <a class="button" href="<?= url('/hebergements') ?>" data-track="cta_click">Découvrir les hébergements</a>
            <a class="button secondary" href="<?= url('/devenir-hote') ?>" data-track="cta_click">Devenir hôte</a>
        </div>
    </div>
    <form class="search-panel" method="get" action="<?= url('/hebergements') ?>" data-track="search_submit">
        <label>Destination<input name="destination" placeholder="Vosges, Oise, Bretagne..."></label>
        <label>Arrivée<input name="start_date" type="date"></label>
        <label>Départ<input name="end_date" type="date"></label>
        <label>Voyageurs<input name="capacity" type="number" min="1" value="2"></label>
        <button class="button full" type="submit">Rechercher</button>
    </form>
</section>

<section class="section">
    <div class="section-heading">
        <p class="eyebrow">Sélection</p>
        <h2>Hébergements à découvrir</h2>
    </div>
    <div class="grid cards">
        <?php foreach ($properties as $property): require __DIR__ . '/_property-card.php'; endforeach; ?>
    </div>
</section>

<section class="section band">
    <h2>Un tourisme plus responsable, sans complexité</h2>
    <div class="features">
        <article><h3>1. Recherchez</h3><p>Filtrez par destination, type de logement, capacité et budget.</p></article>
        <article><h3>2. Réservez fictivement</h3><p>Choisissez vos dates, vérifiez le prix et lancez un paiement test.</p></article>
        <article><h3>3. Voyagez autrement</h3><p>Projetez-vous dans des séjours nature, simples et mémorables.</p></article>
    </div>
</section>

<section class="section testimonials">
    <div>
        <p class="eyebrow">Confiance</p>
        <h2>Des expériences pensées pour se reconnecter</h2>
    </div>
    <blockquote>“Très belle expérience fictive, l’ambiance nature est réussie.” <small>- Tania, voyageuse démo</small></blockquote>
</section>

<section class="section">
    <h2>Catégories insolites</h2>
    <div class="pill-list">
        <a href="<?= url('/hebergements?type=treehouse') ?>">Cabanes</a>
        <a href="<?= url('/hebergements?type=yurt') ?>">Yourtes</a>
        <a href="<?= url('/hebergements?type=floating_cabin') ?>">Cabanes flottantes</a>
        <a href="<?= url('/hebergements?type=tiny_house') ?>">Tiny houses</a>
        <a href="<?= url('/hebergements?type=dome') ?>">Dômes</a>
    </div>
</section>

<section class="section contact-cta">
    <div>
        <p class="eyebrow">Une question ?</p>
        <h2>Préparez votre prochaine escapade insolite</h2>
    </div>
    <a class="button secondary" href="<?= url('/contact') ?>" data-track="cta_click">Nous contacter</a>
</section>
