<div class="table-wrap"><table>
    <thead><tr><th>Logement</th><th>Dates</th><th>Total</th><th>Statut</th><th>Paiement</th><th>Action</th></tr></thead>
    <tbody>
    <?php foreach (($bookings ?? []) as $booking): ?>
        <tr><td><?= e($booking['title']) ?></td><td><?= e($booking['start_date']) ?> → <?= e($booking['end_date']) ?></td><td><?= money($booking['total_price']) ?></td><td><span class="badge"><?= e($booking['status']) ?></span></td><td><?= e($booking['payment_status']) ?></td><td><?php if (($scope ?? 'tenant') === 'tenant'): ?><a href="<?= url('/locataire/reservations/' . $booking['id']) ?>">Voir</a><?php endif; ?></td></tr>
    <?php endforeach; ?>
    </tbody>
</table></div>
