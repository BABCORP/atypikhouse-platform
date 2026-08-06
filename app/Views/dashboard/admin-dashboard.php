<?php require __DIR__ . '/_nav.php'; ?>
<?php
$stats = $stats ?? [];
$alerts = [
    [
        'key' => 'pending_owners',
        'title' => 'Propriétaires à valider',
        'text' => 'compte(s) propriétaire attendent une validation.',
        'url' => '/admin/proprietaires?status=pending',
        'action' => 'Voir les propriétaires',
        'tone' => 'warning',
    ],
    [
        'key' => 'unread_messages',
        'title' => 'Messages non traités',
        'text' => 'message(s) client doivent être consultés.',
        'url' => '/admin/messages',
        'action' => 'Voir les messages',
        'tone' => 'danger',
    ],
    [
        'key' => 'pending_payments',
        'title' => 'Paiements fictifs en attente',
        'text' => 'réservation(s) attendent un paiement de démonstration.',
        'url' => '/admin/reservations?payment_status=not_paid',
        'action' => 'Suivre les paiements',
        'tone' => 'info',
    ],
    [
        'key' => 'pending_reviews',
        'title' => 'Avis à surveiller',
        'text' => 'avis restent dans l’ancien workflow de modération.',
        'url' => '/admin/avis?status=pending',
        'action' => 'Voir les avis',
        'tone' => 'warning',
    ],
];
$priorityAlerts = array_values(array_filter($alerts, static fn (array $alert): bool => (int) ($stats[$alert['key']] ?? 0) > 0));
$statCards = [
    'users' => ['label' => 'Utilisateurs', 'hint' => 'Tous rôles confondus'],
    'tenants' => ['label' => 'Locataires', 'hint' => 'Comptes actifs ou suivis'],
    'owners' => ['label' => 'Propriétaires', 'hint' => 'Hôtes inscrits'],
    'pending_owners' => ['label' => 'Propriétaires en attente', 'hint' => 'Validation admin requise'],
    'published_properties' => ['label' => 'Logements publiés', 'hint' => 'Visibles au catalogue'],
    'paused_properties' => ['label' => 'Logements en pause', 'hint' => 'Invisibles publiquement'],
    'bookings' => ['label' => 'Réservations', 'hint' => 'Total historique'],
    'pending_payment_bookings' => ['label' => 'En attente de paiement', 'hint' => 'Paiement fictif à finaliser'],
    'paid_payments' => ['label' => 'Paiements validés', 'hint' => 'Payés fictivement'],
    'failed_payments' => ['label' => 'Paiements échoués', 'hint' => 'Scénarios de refus'],
    'simulated_revenue' => ['label' => 'Total simulé', 'hint' => 'Aucun encaissement réel'],
    'unread_messages' => ['label' => 'Messages non traités', 'hint' => 'À prendre en charge'],
    'logs_today' => ['label' => 'Logs aujourd’hui', 'hint' => 'Activité sensible'],
];
?>
<section class="section admin-dashboard">
    <div class="section-heading admin-dashboard__hero">
        <div>
            <p class="eyebrow">Back-office</p>
            <h1>Tableau de bord administrateur</h1>
            <p class="muted">Vue d’ensemble des validations, réservations, paiements fictifs, messages et activités récentes.</p>
        </div>
        <a class="button compact ghost" href="<?= url('/admin/logs') ?>">Voir les logs</a>
    </div>

    <div class="admin-alerts">
        <?php foreach ($priorityAlerts as $alert): ?>
            <article role="article" class="admin-alert admin-alert--<?= e($alert['tone']) ?>">
                <span class="admin-alert__icon" aria-hidden="true">!</span>
                <div>
                    <strong><?= e((string) ($stats[$alert['key']] ?? 0)) ?> <?= e($alert['title']) ?></strong>
                    <p><?= e((string) ($stats[$alert['key']] ?? 0)) ?> <?= e($alert['text']) ?></p>
                </div>
                <a class="button compact" href="<?= url($alert['url']) ?>"><?= e($alert['action']) ?></a>
            </article>
        <?php endforeach; ?>
        <?php if (!$priorityAlerts): ?>
            <article role="article" class="admin-alert admin-alert--success">
                <span class="admin-alert__icon" aria-hidden="true">✓</span>
                <div>
                    <strong>Aucune action urgente pour le moment</strong>
                    <p>Les validations et messages prioritaires sont sous contrôle.</p>
                </div>
            </article>
        <?php endif; ?>
    </div>

    <div class="stats admin-stats">
        <?php foreach ($statCards as $key => $meta): ?>
            <article role="article">
                <strong><?= $key === 'simulated_revenue' ? money((float) ($stats[$key] ?? 0)) : e((string) ($stats[$key] ?? 0)) ?></strong>
                <span><?= e($meta['label']) ?></span>
                <small><?= e($meta['hint']) ?></small>
            </article>
        <?php endforeach; ?>
    </div>

    <?php if (!empty($mailDiagnostics)): ?>
        <article role="article" class="panel admin-mail-panel">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Configuration email</p>
                    <h2>Brevo / SMTP Render</h2>
                </div>
                <form role="form" class="inline-form" method="post" action="<?= url('/admin/outils/test-email') ?>">
                    <?= csrf_field() ?>
                    <label class="sr-only" for="test_recipient">Adresse email de test</label>
                    <input id="test_recipient" type="email" name="test_recipient" placeholder="email utilisateur à tester">
                    <button class="button compact" type="submit">Envoyer un email de test</button>
                </form>
            </div>
            <div class="stats compact-stats">
                <?php foreach ([
                    'smtp_enabled' => 'SMTP activé',
                    'real_email_sending' => 'Envoi réel',
                    'demo_mode' => 'Mode démo',
                    'log_only' => 'Log uniquement',
                    'brevo_api_key_defined' => 'API Brevo',
                ] as $key => $label): ?>
                    <article role="article"><strong><?= !empty($mailDiagnostics[$key]) ? 'Oui' : 'Non' ?></strong><span><?= e($label) ?></span></article>
                <?php endforeach; ?>
            </div>
            <p class="notice">Les valeurs sensibles ne sont jamais affichées. Seule leur présence est vérifiée.</p>
        </article>
    <?php endif; ?>

    <div class="actions-row admin-quick-links">
        <a class="button compact" href="<?= url('/admin/proprietaires?status=pending') ?>">Propriétaires à valider</a>
        <a class="button compact" href="<?= url('/admin/reservations?payment_status=not_paid') ?>">Paiements en attente</a>
        <a class="button compact ghost" href="<?= url('/admin/logements') ?>">Gérer les logements</a>
        <a class="button compact ghost" href="<?= url('/admin/reservations') ?>">Gérer les réservations</a>
        <a class="button compact ghost" href="<?= url('/admin/messages') ?>">Messages</a>
        <a class="button compact ghost" href="<?= url('/admin/avis') ?>">Avis</a>
    </div>

    <div class="dashboard-columns admin-dashboard__columns">
        <article role="article" class="panel">
            <h2>Propriétaires à valider</h2>
            <div class="table-wrap"><table>
                <caption>Comptes propriétaires en attente</caption>
                <thead><tr><th>Nom</th><th>Email</th><th>Création</th><th>Action</th></tr></thead>
                <tbody>
                <?php foreach (($pendingOwners ?? []) as $owner): ?>
                    <tr>
                        <td><?= e(trim($owner['first_name'] . ' ' . $owner['last_name'])) ?></td>
                        <td><?= e($owner['email']) ?></td>
                        <td><?= e($owner['created_at']) ?></td>
                        <td><a class="button compact ghost" href="<?= url('/admin/utilisateurs/' . $owner['user_id']) ?>">Voir</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($pendingOwners)): ?><tr><td colspan="4" class="empty-state">Aucun propriétaire en attente.</td></tr><?php endif; ?>
                </tbody>
            </table></div>
        </article>

        <article role="article" class="panel">
            <h2>Messages non traités</h2>
            <?php foreach (($messages ?? []) as $message): ?>
                <div class="admin-list-item">
                    <div>
                        <strong><?= e($message['subject']) ?></strong>
                        <p><?= e($message['name']) ?> · <?= e($message['email']) ?></p>
                    </div>
                    <a class="button compact ghost" href="<?= url('/admin/messages/' . $message['id']) ?>">Traiter</a>
                </div>
            <?php endforeach; ?>
            <?php if (empty($messages)): ?><p class="empty-state">Aucun message non traité.</p><?php endif; ?>
        </article>
    </div>

    <div class="dashboard-columns admin-dashboard__columns">
        <article role="article" class="panel">
            <h2>Réservations récentes</h2>
            <?php $scope = 'admin'; require __DIR__ . '/_booking-table.php'; ?>
        </article>

        <article role="article" class="panel">
            <h2>Paiements fictifs récents</h2>
            <div class="table-wrap"><table>
                <caption>Derniers paiements fictifs</caption>
                <thead><tr><th>Réservation</th><th>Locataire</th><th>Montant</th><th>Statut</th><th>Référence</th></tr></thead>
                <tbody>
                <?php foreach (($payments ?? []) as $payment): ?>
                    <tr>
                        <td><a href="<?= url('/admin/reservations/' . $payment['booking_id']) ?>">#<?= (int) $payment['booking_id'] ?></a><br><small><?= e($payment['title']) ?></small></td>
                        <td><?= e($payment['tenant_email']) ?></td>
                        <td><?= money($payment['amount']) ?></td>
                        <td><span class="badge <?= e($payment['status']) ?>"><?= status_label($payment['status']) ?></span></td>
                        <td><?= e($payment['test_transaction_id']) ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?><tr><td colspan="5" class="empty-state">Aucun paiement fictif enregistré.</td></tr><?php endif; ?>
                </tbody>
            </table></div>
        </article>
    </div>

    <div class="dashboard-columns admin-dashboard__columns">
        <article role="article" class="panel">
            <h2>Logements à surveiller</h2>
            <?php $properties = $properties ?? []; $scope = 'admin'; require __DIR__ . '/_property-table.php'; ?>
        </article>
        <article role="article" class="panel">
            <h2>Derniers logs d’audit</h2>
            <div class="table-wrap"><table>
                <caption>Dernières actions sensibles</caption>
                <thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Entité</th></tr></thead>
                <tbody>
                <?php foreach (($logs ?? []) as $log): ?>
                    <tr>
                        <td><?= e($log['created_at']) ?></td>
                        <td><?= e($log['email'] ?? 'invité') ?></td>
                        <td><span class="badge muted"><?= e($log['action']) ?></span></td>
                        <td><?= e($log['entity_type'] . ' #' . $log['entity_id']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table></div>
        </article>
    </div>
</section>
