<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1>Bonjour <?= e($user['first_name']) ?></h1>
    <p>Retrouvez vos séjours à venir et vos réservations passées.</p>
    <a class="button" href="<?= url('/hebergements') ?>">Découvrir les hébergements</a>
    <?php $bookings = $bookings ?? []; require __DIR__ . '/_booking-table.php'; ?>
    <section class="dashboard-section">
        <div class="section-heading">
            <div><p class="eyebrow">Inspiration</p><h2>Mes favoris</h2></div>
            <a class="text-link" href="<?= url('/locataire/favoris') ?>">Voir tous mes favoris</a>
        </div>
        <?php if (!empty($favorites)): ?>
            <div class="grid cards compact-cards">
                <?php foreach ($favorites as $property): require dirname(__DIR__) . '/public/_property-card.php'; endforeach; ?>
            </div>
        <?php else: ?>
            <p class="empty-state">Aucun logement favori pour le moment.</p>
        <?php endif; ?>
    </section>
</section>
