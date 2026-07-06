<section class="property-detail-page">
    <div class="property-detail-container">
        <header class="property-heading">
            <p class="eyebrow"><?= e($property['city']) ?> · <?= e($property['region']) ?></p>
            <h1><?= e($property['title']) ?></h1>
            <p class="lead"><?= e($property['short_description']) ?></p>
        </header>
        <div class="property-detail-layout">
            <article class="property-main-content">
                <div class="property-hero-image property-gallery">
                    <?php foreach ($images as $image): ?>
                        <img loading="lazy" src="<?= image_url($image['image_path']) ?>" alt="<?= e($image['alt_text']) ?>">
                    <?php endforeach; ?>
                </div>
                <section class="property-section">
                    <h2>À propos du logement</h2>
                    <p><?= nl2br(e($property['long_description'])) ?></p>
                </section>
                <section class="property-section">
                    <h2>Équipements</h2>
                    <?php if ($amenities): ?>
                        <div class="pill-list amenity-list"><?php foreach ($amenities as $amenity): ?><span><?= e($amenity) ?></span><?php endforeach; ?></div>
                    <?php else: ?>
                        <p class="empty-state">Aucun équipement renseigné pour le moment.</p>
                    <?php endif; ?>
                </section>
                <section class="property-section">
                    <h2>Avis publiés</h2>
                    <?php foreach ($reviews as $review): ?><blockquote class="review-card"><strong><?= (int) $review['rating'] ?>/5</strong> — <?= e($review['comment']) ?><br><small><?= e($review['first_name']) ?></small></blockquote><?php endforeach; ?>
                    <?php if (!$reviews): ?><p class="empty-state">Aucun avis publié pour le moment.</p><?php endif; ?>
                </section>
                <section class="property-section">
                    <h2>Autres séjours du même univers</h2>
                    <?php $hasRelated = false; ?>
                    <div class="grid cards compact-cards">
                        <?php foreach ($related as $relatedProperty): ?>
                            <?php if ((int) $relatedProperty['id'] !== (int) $property['id']): ?>
                                <?php $hasRelated = true; $currentProperty = $property; $property = $relatedProperty; require __DIR__ . '/_property-card.php'; $property = $currentProperty; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    <?php if (!$hasRelated): ?><p class="empty-state">Aucun séjour similaire disponible pour le moment.</p><?php endif; ?>
                </section>
            </article>
            <aside class="booking-box booking-card property-booking-aside" aria-label="Réservation fictive">
                <div class="booking-card-header">
                    <h2><?= money($property['price_per_night']) ?> <span>/ nuit</span></h2>
                    <p><?= (int) $property['capacity'] ?> voyageurs · <?= (int) $property['bedrooms'] ?> chambre(s)</p>
                </div>
                <form method="post" action="<?= url('/reservation/' . $property['id']) ?>" data-track="booking_start">
                    <?= csrf_field() ?>
                    <label>Arrivée<input required name="start_date" type="date"></label>
                    <label>Départ<input required name="end_date" type="date"></label>
                    <label>Voyageurs<input required name="guests_count" type="number" min="1" max="<?= (int) $property['capacity'] ?>" value="2"></label>
                    <button class="button full" type="submit">Réserver ce séjour</button>
                </form>
                <div class="public-calendar-block">
                    <div class="calendar-toolbar">
                        <button class="button ghost compact" type="button" data-calendar-prev>Précédent</button>
                        <h3 data-calendar-title>Disponibilités</h3>
                        <button class="button ghost compact" type="button" data-calendar-next>Suivant</button>
                    </div>
                    <div class="calendar-legend" aria-label="Légende des disponibilités">
                        <span><i class="legend-dot available"></i>Disponible</span>
                        <span><i class="legend-dot unavailable"></i>Indisponible</span>
                        <span><i class="legend-dot override"></i>Prix spécifique</span>
                        <span><i class="legend-dot booked"></i>Réservé</span>
                    </div>
                    <div class="availability-calendar" data-availability-calendar data-calendar-payload="<?= e(json_encode($calendarData ?? [], JSON_UNESCAPED_UNICODE)) ?>" aria-live="polite"></div>
                </div>
                <div class="booking-side-section">
                    <h3>Prochaines disponibilités</h3>
                    <div class="availability-list">
                        <?php foreach (array_slice($availabilities, 0, 8) as $availability): ?>
                            <span class="<?= $availability['is_available'] ? 'available' : 'unavailable' ?>"><?= e(date('d/m', strtotime($availability['date']))) ?> <?= $availability['is_available'] ? 'libre' : 'indisponible' ?></span>
                        <?php endforeach; ?>
                        <?php if (!$availabilities): ?><span class="unavailable">Aucune date publiée</span><?php endif; ?>
                    </div>
                </div>
                <p class="notice"><?= e(config('academic_disclaimer')) ?></p>
                <div class="booking-host">
                    <h3>Hôte</h3>
                    <p><?= e($property['company_name'] ?: ($property['first_name'] . ' ' . $property['last_name'])) ?></p>
                </div>
            </aside>
        </div>
    </div>
</section>
