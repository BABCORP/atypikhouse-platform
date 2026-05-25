<article class="card property-card">
    <img loading="lazy" src="<?= image_url($property['main_image'] ?? null) ?>" alt="<?= e($property['title']) ?>">
    <div class="card-body">
        <p class="eyebrow"><?= e(str_replace('_', ' ', $property['type'])) ?> · <?= e($property['city']) ?></p>
        <h3><a href="<?= url('/hebergements/' . $property['slug']) ?>"><?= e($property['title']) ?></a></h3>
        <p><?= e($property['short_description']) ?></p>
        <div class="meta-line">
            <span><?= (int) $property['capacity'] ?> pers.</span>
            <span><?= money($property['price_per_night']) ?>/nuit</span>
            <span><?= number_format((float) ($property['avg_rating'] ?? 0), 1, ',', ' ') ?>/5</span>
        </div>
        <a class="button full" href="<?= url('/hebergements/' . $property['slug']) ?>" data-track="property_view">Voir les disponibilités</a>
    </div>
</article>
