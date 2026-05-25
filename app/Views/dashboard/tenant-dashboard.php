<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1>Bonjour <?= e($user['first_name']) ?></h1>
    <p>Retrouvez vos séjours à venir et vos réservations passées.</p>
    <a class="button" href="<?= url('/hebergements') ?>">Découvrir les hébergements</a>
    <?php $bookings = $bookings ?? []; require __DIR__ . '/_booking-table.php'; ?>
</section>
