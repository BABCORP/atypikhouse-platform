<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Back-office</p>
            <h1>Réservation #<?= (int) $booking['id'] ?></h1>
            <p><?= e(config('academic_disclaimer')) ?></p>
        </div>
        <a class="button ghost" href="<?= url('/admin/reservations') ?>">Retour</a>
    </div>
    <div class="dashboard-grid two">
        <article class="panel">
            <h2>Séjour</h2>
            <dl class="detail-list">
                <dt>Logement</dt><dd><a href="<?= url('/hebergements/' . $booking['slug']) ?>"><?= e($booking['title']) ?></a></dd>
                <dt>Ville</dt><dd><?= e($booking['city']) ?></dd>
                <dt>Dates</dt><dd><?= e($booking['start_date']) ?> → <?= e($booking['end_date']) ?></dd>
                <dt>Nuits</dt><dd><?= (int) $booking['nights'] ?></dd>
                <dt>Voyageurs</dt><dd><?= (int) $booking['guests_count'] ?></dd>
            </dl>
        </article>
        <article class="panel">
            <h2>Locataire et paiement</h2>
            <dl class="detail-list">
                <dt>Locataire</dt><dd><?= e($booking['tenant_first_name'] . ' ' . $booking['tenant_last_name']) ?></dd>
                <dt>Email</dt><dd><?= e($booking['tenant_email']) ?></dd>
                <dt>Sous-total</dt><dd><?= money($booking['subtotal']) ?></dd>
                <dt>Ménage</dt><dd><?= money($booking['cleaning_fee']) ?></dd>
                <dt>Total simulé</dt><dd><strong><?= money($booking['total_price']) ?></strong></dd>
                <dt>Transaction test</dt><dd><?= e($booking['test_transaction_id'] ?: 'Aucune') ?></dd>
            </dl>
        </article>
    </div>
    <form class="panel inline-admin-form" method="post" action="<?= url('/admin/reservations/' . $booking['id'] . '/statut') ?>">
        <?= csrf_field() ?>
        <label>Statut
            <select name="status">
                <?php foreach (['confirmed', 'cancelled', 'completed'] as $status): ?>
                    <option value="<?= $status ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button" type="submit">Mettre à jour</button>
        <p><span class="badge <?= e($booking['status']) ?>"><?= status_label($booking['status']) ?></span> <span class="badge"><?= status_label($booking['payment_status']) ?></span></p>
    </form>
</section>
