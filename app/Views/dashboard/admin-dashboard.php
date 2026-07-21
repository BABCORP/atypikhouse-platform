<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1>Administration AtypikHouse</h1>
    <?php $statLabels = [
        'users' => 'utilisateurs',
        'owners' => 'propriétaires',
        'tenants' => 'locataires',
        'admins' => 'administrateurs',
        'pending_users' => 'comptes à valider',
        'suspended_users' => 'comptes suspendus',
        'published_properties' => 'logements publiés',
        'pending_properties' => 'logements à valider',
        'pending_property_changes' => 'modifications à valider',
        'rejected_properties' => 'logements refusés',
        'paused_properties' => 'logements en pause',
        'deleted_properties' => 'logements supprimés',
        'bookings' => 'réservations',
        'pending_bookings' => 'réservations à valider',
        'pending_payment_bookings' => 'réservations en attente de paiement',
        'confirmed_bookings' => 'réservations confirmées',
        'cancelled_bookings' => 'réservations annulées',
        'completed_bookings' => 'réservations terminées',
        'pending_payments' => 'paiements en attente',
        'paid_payments' => 'paiements fictifs validés',
        'failed_payments' => 'paiements fictifs échoués',
        'pending_reviews' => 'avis à modérer',
        'published_reviews' => 'avis publiés',
        'rejected_reviews' => 'avis refusés',
        'unread_messages' => 'messages non traités',
        'read_messages' => 'messages lus',
        'processed_messages' => 'messages traités',
        'simulated_revenue' => 'revenus de démonstration',
    ]; ?>
    <div class="stats">
        <?php foreach ($stats as $label => $value): ?><article><strong><?= is_numeric($value) && str_contains($label, 'revenue') ? money($value) : e((string)$value) ?></strong><span><?= e($statLabels[$label] ?? $label) ?></span></article><?php endforeach; ?>
    </div>
    <?php if (!empty($mailDiagnostics)): ?>
        <article class="panel">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Configuration email</p>
                    <h2>SMTP Render</h2>
                </div>
                <form method="post" action="<?= url('/admin/outils/test-email') ?>">
                    <?= csrf_field() ?>
                    <button class="button compact" type="submit">Envoyer un email de test</button>
                </form>
            </div>
            <div class="stats compact-stats">
                <?php foreach ([
                    'smtp_enabled' => 'SMTP activé',
                    'real_email_sending' => 'Envoi réel demandé',
                    'demo_mode' => 'Mode démo',
                    'log_only' => 'Journalisation seule',
                    'mail_host_defined' => 'MAIL_HOST défini',
                    'mail_username_defined' => 'MAIL_USERNAME défini',
                    'mail_password_defined' => 'MAIL_PASSWORD défini',
                    'mail_from_defined' => 'Expéditeur défini',
                    'admin_email_defined' => 'Admin email défini',
                ] as $key => $label): ?>
                    <article><strong><?= !empty($mailDiagnostics[$key]) ? 'Oui' : 'Non' ?></strong><span><?= e($label) ?></span></article>
                <?php endforeach; ?>
            </div>
            <p class="notice">Les valeurs sensibles ne sont jamais affichées. Seule leur présence est vérifiée.</p>
        </article>
    <?php endif; ?>
    <h2>Logements en attente de validation</h2>
    <?php $properties = $properties ?? []; $scope = 'admin'; require __DIR__ . '/_property-table.php'; ?>
    <?php if (!$properties): ?><p>Aucun logement en attente.</p><?php endif; ?>
    <h2>Modifications de logements à valider</h2>
    <?php if (!empty($propertyChanges)): ?>
        <div class="table-wrap"><table>
            <caption>Demandes de modification propriétaires</caption>
            <thead><tr><th>Logement</th><th>Propriétaire</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($propertyChanges as $request): ?>
                <tr>
                    <td><?= e($request['title']) ?></td>
                    <td><?= e($request['owner_email']) ?></td>
                    <td><?= e($request['updated_at']) ?></td>
                    <td><a class="button compact ghost" href="<?= url('/admin/logements/modifications/' . $request['id']) ?>">Comparer</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    <?php else: ?><p>Aucune modification en attente.</p><?php endif; ?>
    <div class="actions-row">
        <a class="button compact" href="<?= url('/admin/utilisateurs') ?>">Gérer les utilisateurs</a>
        <a class="button compact" href="<?= url('/admin/utilisateurs?status=pending') ?>">Comptes à valider</a>
        <a class="button compact" href="<?= url('/admin/logements?status=pending') ?>">Valider les logements</a>
        <a class="button compact" href="<?= url('/admin/logements/modifications') ?>">Voir les modifications à valider</a>
        <a class="button compact ghost" href="<?= url('/admin/reservations?status=pending_admin') ?>">Réservations à valider</a>
        <a class="button compact ghost" href="<?= url('/admin/reservations?status=pending_payment') ?>">Réservations en attente de paiement</a>
        <a class="button compact ghost" href="<?= url('/admin/reservations?payment_status=not_paid') ?>">Paiements en attente</a>
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
