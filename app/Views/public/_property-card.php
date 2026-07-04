<article class="card property-card">
    <div class="card-media">
        <img loading="lazy" src="<?= image_url($property['main_image'] ?? null) ?>" alt="<?= e($property['title']) ?>">
        <span class="eco-badge">Éco-lodge</span>
    </div>
    <div class="card-body">
        <h3><a href="<?= url('/hebergements/' . $property['slug']) ?>"><?= e($property['title']) ?></a></h3>
        <p class="card-location"><?= e($property['city']) ?> · <?= property_type_label($property['type']) ?></p>
        <div class="meta-line">
            <span><?= (int) $property['capacity'] ?> voyageur(s)</span>
            <span aria-label="Note"><?= number_format((float) ($property['avg_rating'] ?? 0), 1, ',', ' ') ?>/5</span>
        </div>
        <div class="card-action"><strong><?= money($property['price_per_night']) ?> <small>/ nuit</small></strong><a class="button compact" href="<?= url('/hebergements/' . $property['slug']) ?>" data-track="property_view">Voir</a></div>
    </div>
</article>
