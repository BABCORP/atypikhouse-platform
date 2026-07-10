<?php
$favoriteIds = $favoriteIds ?? [];
$isFavorite = in_array((int) $property['id'], array_map('intval', $favoriteIds), true);
$rating = (float) ($property['avg_rating'] ?? 0);
$reviewsCount = (int) ($property['reviews_count'] ?? 0);
$favoriteRedirect = $_SERVER['REQUEST_URI'] ?? '/hebergements';
if (!is_string($favoriteRedirect) || !str_starts_with($favoriteRedirect, '/') || str_starts_with($favoriteRedirect, '//')) {
    $favoriteRedirect = '/hebergements';
}
?>
<article class="card property-card">
    <div class="card-media">
        <img loading="lazy" src="<?= image_url($property['main_image'] ?? null) ?>" alt="<?= e($property['title']) ?>">
        <span class="eco-badge">Éco-lodge</span>
        <form class="favorite-form" method="post" action="<?= url('/favoris/' . $property['id'] . '/toggle') ?>">
            <?= csrf_field() ?>
            <input type="hidden" name="redirect" value="<?= e($favoriteRedirect) ?>">
            <button class="favorite-button<?= $isFavorite ? ' is-favorite' : '' ?>" type="submit" aria-label="<?= $isFavorite ? 'Retirer ce logement des favoris' : 'Ajouter ce logement aux favoris' ?>">
                <span aria-hidden="true"><?= $isFavorite ? '♥' : '♡' ?></span>
            </button>
        </form>
    </div>
    <div class="card-body">
        <h3><a href="<?= url('/hebergements/' . $property['slug']) ?>"><?= e($property['title']) ?></a></h3>
        <p class="card-location"><?= e($property['city']) ?> · <?= property_type_label($property['type']) ?></p>
        <div class="meta-line">
            <span><?= (int) $property['capacity'] ?> voyageur(s)</span>
            <span class="rating-summary rating-summary--compact">
                <?= rating_stars($rating, 'Note moyenne') ?>
                <span><?= number_format($rating, 1, ',', ' ') ?>/5 · <?= $reviewsCount ?> avis</span>
            </span>
        </div>
        <div class="card-action"><strong><?= money($property['price_per_night']) ?> <small>/ nuit</small></strong><a class="button compact" href="<?= url('/hebergements/' . $property['slug']) ?>" data-track="property_view">Voir</a></div>
    </div>
</article>
