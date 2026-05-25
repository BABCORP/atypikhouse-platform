<section class="page-hero compact">
    <p class="eyebrow">Accueil · Logements</p>
    <h1>Trouvez votre séjour insolite</h1>
    <p>Explorez une sélection d’hébergements responsables pour vivre une parenthèse unique en pleine nature.</p>
</section>
<section class="section">
    <form class="filters catalogue-search" method="get" data-track="search_submit">
        <label>Destination<input name="destination" value="<?= e($filters['location']) ?>"></label>
        <label>Type<select name="type">
            <option value="">Tous</option>
            <?php foreach (['treehouse'=>'Cabane','yurt'=>'Yourte','floating_cabin'=>'Cabane flottante','tiny_house'=>'Tiny house','dome'=>'Dôme','other'=>'Autre'] as $value=>$label): ?>
                <option value="<?= $value ?>" <?= $filters['type'] === $value ? 'selected' : '' ?>><?= $label ?></option>
            <?php endforeach; ?>
        </select></label>
        <details class="filter-more" open>
        <summary>Plus de filtres</summary>
        <div class="filter-more-grid">
        <label>Capacité min.<input name="capacity" type="number" min="1" value="<?= e($filters['capacity']) ?>"></label>
        <label>Prix max<input name="max_price" type="number" min="0" value="<?= e($filters['max_price']) ?>"></label>
        <label>Équipement<input name="amenity" placeholder="Spa, Wifi..." value="<?= e($filters['amenity']) ?>"></label>
        <label>Arrivée<input name="start_date" type="date" value="<?= e($filters['start_date']) ?>"></label>
        <label>Départ<input name="end_date" type="date" value="<?= e($filters['end_date']) ?>"></label>
        <label>Tri<select name="sort"><option value="newest">Nouveautés</option><option value="price_asc" <?= $filters['sort']==='price_asc'?'selected':'' ?>>Prix croissant</option><option value="price_desc" <?= $filters['sort']==='price_desc'?'selected':'' ?>>Prix décroissant</option></select></label>
        </div>
        </details>
        <button class="button" type="submit">Rechercher</button>
    </form>
    <div class="results-heading"><h2><?= count($properties) ?> logement(s) trouvé(s)</h2><p>Hébergements publiés après validation</p></div>
    <div class="grid cards">
        <?php foreach ($properties as $property): require __DIR__ . '/_property-card.php'; endforeach; ?>
        <?php if (!$properties): ?><p>Aucun logement publié ne correspond à votre recherche.</p><?php endif; ?>
    </div>
</section>
