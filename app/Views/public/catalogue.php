<section class="search-hero catalogue-search-hero">
    <div class="search-hero__inner">
        <div class="search-hero__intro">
            <p class="eyebrow">Accueil · Logements</p>
            <h1>Trouvez votre séjour insolite</h1>
            <p>Filtrez les hébergements selon vos envies et trouvez le lieu idéal pour une parenthèse nature, authentique et dépaysante.</p>
        </div>
        <form role="search" aria-label="Recherche dans le catalogue des hébergements" class="catalogue-search search-card" method="get" data-track="search_submit">
            <div class="search-form__grid">
                <label>Destination<input list="destinations-list" name="destination" value="<?= e($filters['location']) ?>" placeholder="Ex. Pierrefonds, Gérardmer, Amiens…"></label>
                <datalist id="destinations-list">
                    <?php foreach (($destinations ?? []) as $destination): ?><option value="<?= e($destination) ?>"></option><?php endforeach; ?>
                </datalist>
                <label>Type<select name="type">
                    <option value="">Tous</option>
                    <?php foreach (['treehouse'=>'Cabane','yurt'=>'Yourte','floating_cabin'=>'Cabane flottante','tiny_house'=>'Tiny house','dome'=>'Dôme','other'=>'Autre'] as $value=>$label): ?>
                        <option value="<?= $value ?>" <?= $filters['type'] === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select></label>
                <label>Capacité min.<input name="capacity" type="number" min="1" value="<?= e($filters['capacity']) ?>" placeholder="2"></label>
                <label>Prix max<input name="max_price" type="number" min="0" value="<?= e($filters['max_price']) ?>" placeholder="180"></label>
                <label>Équipement<input name="amenity" placeholder="Spa, terrasse..." value="<?= e($filters['amenity']) ?>"></label>
                <label>Arrivée<input name="start_date" type="date" value="<?= e($filters['start_date']) ?>"></label>
                <label>Départ<input name="end_date" type="date" value="<?= e($filters['end_date']) ?>"></label>
                <label>Tri<select name="sort"><option value="newest">Nouveautés</option><option value="price_asc" <?= $filters['sort']==='price_asc'?'selected':'' ?>>Prix croissant</option><option value="price_desc" <?= $filters['sort']==='price_desc'?'selected':'' ?>>Prix décroissant</option></select></label>
            </div>
            <div class="search-form__actions">
                <button class="button" type="submit">Rechercher</button>
            </div>
        </form>
    </div>
</section>
<section class="section catalogue-results">
    <div class="results-heading"><h2><?= count($properties) ?> logement(s) trouvé(s)</h2><p>Hébergements publiés après validation</p></div>
    <div class="grid cards">
        <?php foreach ($properties as $property): require __DIR__ . '/_property-card.php'; endforeach; ?>
        <?php if (!$properties): ?><p>Aucun logement publié ne correspond à votre recherche.</p><?php endif; ?>
    </div>
</section>
