<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1>Administration AtypikHouse</h1>
    <?php $statLabels = ['users' => 'utilisateurs', 'owners' => 'propriétaires', 'tenants' => 'locataires', 'published_properties' => 'logements publiés', 'pending_properties' => 'logements à valider', 'confirmed_bookings' => 'réservations confirmées', 'pending_reviews' => 'avis à modérer', 'simulated_revenue' => 'revenus simulés']; ?>
    <div class="stats">
        <?php foreach ($stats as $label => $value): ?><article><strong><?= is_numeric($value) && str_contains($label, 'revenue') ? money($value) : e((string)$value) ?></strong><span><?= e($statLabels[$label] ?? $label) ?></span></article><?php endforeach; ?>
    </div>
    <h2>Logements en attente de validation</h2>
    <?php $properties = $properties ?? []; require __DIR__ . '/_property-table.php'; ?>
    <?php if (!$properties): ?><p>Aucun logement en attente.</p><?php endif; ?>
    <h2>Dernières réservations</h2>
    <?php $scope = 'admin'; require __DIR__ . '/_booking-table.php'; ?>
    <div class="dashboard-columns">
        <div>
            <h2>Avis à modérer</h2>
            <?php foreach ($reviews as $review): ?><article class="panel"><strong><?= e($review['title']) ?></strong><p><?= (int) $review['rating'] ?>/5 - <?= e($review['comment']) ?></p></article><?php endforeach; ?>
            <?php if (!$reviews): ?><p>Aucun avis en attente.</p><?php endif; ?>
        </div>
        <div>
            <h2>Derniers messages</h2>
            <?php foreach ($messages as $message): ?><article class="panel"><strong><?= e($message['subject']) ?></strong><p><?= e($message['name']) ?> - <?= e($message['status']) ?></p></article><?php endforeach; ?>
        </div>
    </div>
</section>
