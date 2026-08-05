<div class="table-wrap"><table>
    <caption><?= ($scope ?? 'tenant') === 'admin' ? 'Liste de toutes les réservations' : (($scope ?? 'tenant') === 'owner' ? 'Réservations reçues' : 'Mes réservations') ?></caption>
    <thead><tr><th>Logement</th><th>Dates</th><th>Total</th><th>Statut</th><th>Paiement</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach (($bookings ?? []) as $booking): ?>
        <tr>
            <td><a href="<?= url('/hebergements/' . $booking['slug']) ?>"><?= e($booking['title']) ?></a></td>
            <td><?= e($booking['start_date']) ?> → <?= e($booking['end_date']) ?><br><small><?= (int) $booking['nights'] ?> nuit(s)</small></td>
            <td><?= money($booking['total_price']) ?></td>
            <td><span class="badge <?= e($booking['status']) ?>"><?= status_label($booking['status']) ?></span></td>
            <td><span class="badge muted"><?= status_label($booking['payment_status']) ?></span></td>
            <td>
                <?php if (($scope ?? 'tenant') === 'tenant'): ?>
                    <a class="button compact ghost" href="<?= url('/locataire/reservations/' . $booking['id']) ?>">Voir</a>
                    <?php if ($booking['status'] === 'pending_payment' && in_array($booking['payment_status'], ['not_paid', 'test_failed'], true)): ?>
                        <a class="button compact" href="<?= url('/paiement/' . $booking['id']) ?>">Payer</a>
                    <?php endif; ?>
                    <?php if (!empty($booking['can_review'])): ?>
                        <a class="button compact" href="<?= url('/locataire/reservations/' . $booking['id']) ?>#deposer-avis">Déposer un avis</a>
                    <?php elseif (!empty($booking['review_id'])): ?>
                        <a class="button compact ghost" href="<?= url('/locataire/avis') ?>">Avis transmis</a>
                    <?php endif; ?>
                <?php elseif (($scope ?? '') === 'admin'): ?>
                    <a class="button compact ghost" href="<?= url('/admin/reservations/' . $booking['id']) ?>">Détail</a>
                <?php else: ?>
                    <a class="button compact ghost" href="<?= url('/hebergements/' . $booking['slug']) ?>">Fiche</a>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($bookings)): ?>
        <tr><td colspan="6" class="empty-state">Aucune réservation ne correspond aux critères.</td></tr>
    <?php endif; ?>
    </tbody>
</table></div>
