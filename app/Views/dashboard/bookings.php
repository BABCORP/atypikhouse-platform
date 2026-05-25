<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1><?= e($title) ?></h1>
    <?php require __DIR__ . '/_booking-table.php'; ?>
    <?php if (($scope ?? '') === 'admin'): ?>
        <?php foreach ($bookings as $booking): ?><form class="inline-form" method="post" action="<?= url('/admin/reservations/' . $booking['id'] . '/statut') ?>"><?= csrf_field() ?><select name="status"><option>confirmed</option><option>cancelled</option><option>completed</option></select><button>Modifier #<?= (int)$booking['id'] ?></button></form><?php endforeach; ?>
    <?php endif; ?>
</section>
