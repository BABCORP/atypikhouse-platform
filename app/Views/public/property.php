<section class="section property-detail">
    <header class="property-heading">
        <p class="eyebrow"><?= e($property['city']) ?> · <?= e($property['region']) ?></p>
        <h1><?= e($property['title']) ?></h1>
        <p class="lead"><?= e($property['short_description']) ?></p>
    </header>
    <div class="gallery">
        <?php foreach ($images as $image): ?>
            <img src="<?= image_url($image['image_path']) ?>" alt="<?= e($image['alt_text']) ?>">
        <?php endforeach; ?>
    </div>
    <div class="detail-grid">
        <article>
            <p><?= nl2br(e($property['long_description'])) ?></p>
            <h2>Équipements</h2>
            <div class="pill-list"><?php foreach ($amenities as $amenity): ?><span><?= e($amenity) ?></span><?php endforeach; ?></div>
            <h2>Avis publiés</h2>
            <?php foreach ($reviews as $review): ?><blockquote><strong><?= (int) $review['rating'] ?>/5</strong> — <?= e($review['comment']) ?><br><small><?= e($review['first_name']) ?></small></blockquote><?php endforeach; ?>
            <?php if (!$reviews): ?><p>Aucun avis publié pour le moment.</p><?php endif; ?>
            <h2>Autres séjours du même univers</h2>
            <div class="grid cards compact-cards">
                <?php foreach ($related as $relatedProperty): ?>
                    <?php if ((int) $relatedProperty['id'] !== (int) $property['id']): ?>
                        <?php $currentProperty = $property; $property = $relatedProperty; require __DIR__ . '/_property-card.php'; $property = $currentProperty; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </article>
        <aside class="booking-box">
            <h2><?= money($property['price_per_night']) ?> / nuit</h2>
            <p><?= (int) $property['capacity'] ?> voyageurs · <?= (int) $property['bedrooms'] ?> chambre(s)</p>
            <form method="post" action="<?= url('/reservation/' . $property['id']) ?>" data-track="booking_start">
                <?= csrf_field() ?>
                <label>Arrivée<input required name="start_date" type="date"></label>
                <label>Départ<input required name="end_date" type="date"></label>
                <label>Voyageurs<input required name="guests_count" type="number" min="1" max="<?= (int) $property['capacity'] ?>" value="2"></label>
                <button class="button full" type="submit">Réserver ce séjour</button>
            </form>
            <h3>Prochaines disponibilités</h3>
            <div class="availability-list">
                <?php foreach (array_slice($availabilities, 0, 8) as $availability): ?>
                    <span class="<?= $availability['is_available'] ? 'available' : 'unavailable' ?>"><?= e(date('d/m', strtotime($availability['date']))) ?> <?= $availability['is_available'] ? 'libre' : 'indisponible' ?></span>
                <?php endforeach; ?>
            </div>
            <p class="notice"><?= e(config('academic_disclaimer')) ?></p>
            <h3>Hôte</h3>
            <p><?= e($property['company_name'] ?: ($property['first_name'] . ' ' . $property['last_name'])) ?></p>
        </aside>
    </div>
</section>
