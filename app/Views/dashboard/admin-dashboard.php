<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1>Administration AtypikHouse</h1>
    <?php $statLabels = ['users' => 'utilisateurs', 'owners' => 'propriétaires', 'tenants' => 'locataires', 'admins' => 'administrateurs', 'published_properties' => 'logements publiés', 'pending_properties' => 'logements à valider', 'rejected_properties' => 'logements refusés', 'bookings' => 'réservations', 'pending_bookings' => 'paiements en attente', 'confirmed_bookings' => 'réservations confirmées', 'cancelled_bookings' => 'réservations annulées', 'pending_reviews' => 'avis à modérer', 'unread_messages' => 'messages non traités', 'simulated_revenue' => 'revenus simulés']; ?>
    <div class="stats">
        <?php foreach ($stats as $label => $value): ?><article><strong><?= is_numeric($value) && str_contains($label, 'revenue') ? money($value) : e((string)$value) ?></strong><span><?= e($statLabels[$label] ?? $label) ?></span></article><?php endforeach; ?>
    </div>
    <h2>Logements en attente de validation</h2>
    <?php $properties = $properties ?? []; $scope = 'admin'; require __DIR__ . '/_property-table.php'; ?>
    <?php if (!$properties): ?><p>Aucun logement en attente.</p><?php endif; ?>
    <div class="actions-row">
        <a class="button compact" href="<?= url('/admin/utilisateurs') ?>">Gérer les utilisateurs</a>
        <a class="button compact" href="<?= url('/admin/logements?status=pending') ?>">Valider les logements</a>
        <a class="button compact ghost" href="<?= url('/admin/reservations') ?>">Voir les réservations</a>
        <a class="button compact ghost" href="<?= url('/admin/avis?status=pending') ?>">Modérer les avis</a>
        <a class="button compact ghost" href="<?= url('/admin/messages') ?>">Voir les messages</a>
        <a class="button compact ghost" href="<?= url('/admin/logs') ?>">Voir les logs</a>
    </div>
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
            <?php foreach ($messages as $message): ?><article class="panel"><strong><?= e($message['subject']) ?></strong><p><?= e($message['name']) ?> - <?= status_label($message['status']) ?></p><a class="button compact ghost" href="<?= url('/admin/messages/' . $message['id']) ?>">Ouvrir</a></article><?php endforeach; ?>
        </div>
    </div>
    <h2>Derniers logs d’audit</h2>
    <div class="table-wrap"><table>
        <caption>Dernières actions sensibles</caption>
        <thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Entité</th></tr></thead>
        <tbody>
        <?php foreach (($logs ?? []) as $log): ?>
            <tr><td><?= e($log['created_at']) ?></td><td><?= e($log['email'] ?? 'invité') ?></td><td><span class="badge muted"><?= e($log['action']) ?></span></td><td><?= e($log['entity_type'] . ' #' . $log['entity_id']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</section>
