<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Traçabilité</p><h1>Journaux d’audit</h1></div></div>
    <form class="filters panel" method="get">
        <label>Action<input name="action" value="<?= e(input('action', '')) ?>" placeholder="login, booking, rgpd"></label>
        <label>Email utilisateur<input name="email" value="<?= e(input('email', '')) ?>" placeholder="admin@..."></label>
        <label>Du<input type="date" name="start_date" value="<?= e(input('start_date', '')) ?>"></label>
        <label>Au<input type="date" name="end_date" value="<?= e(input('end_date', '')) ?>"></label>
        <button class="button compact" type="submit">Filtrer</button>
    </form>
    <div class="table-wrap"><table>
        <caption>Actions sensibles enregistrées en lecture seule</caption>
        <thead><tr><th>Date</th><th>Utilisateur</th><th>Action</th><th>Entité</th><th>IP</th></tr></thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
            <tr><td><?= e($log['created_at']) ?></td><td><?= e($log['email'] ?? 'invité') ?></td><td><span class="badge muted"><?= e($log['action']) ?></span></td><td><?= e($log['entity_type'] . ' #' . $log['entity_id']) ?></td><td><?= e($log['ip_address']) ?></td></tr>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?><tr><td colspan="5" class="empty-state">Aucun log ne correspond aux critères.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
    <?php require __DIR__ . '/_pagination.php'; ?>
</section>
