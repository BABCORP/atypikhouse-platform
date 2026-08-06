<section class="section form-layout narrow">
    <form role="form" class="panel payment-demo-card" method="post" data-track="booking_confirm">
        <p class="eyebrow">Simulation de paiement</p>
        <h1>Paiement fictif de démonstration</h1>
        <?= csrf_field() ?>
        <p class="muted">Aucune carte bancaire réelle n’est demandée. Cette étape sert uniquement à confirmer le parcours de réservation dans le cadre du projet.</p>
        <dl class="detail-list">
            <dt>Réservation</dt><dd>#<?= (int) $booking['id'] ?></dd>
            <dt>Logement</dt><dd><?= e($booking['title']) ?>, <?= e($booking['city']) ?></dd>
            <dt>Dates</dt><dd><?= e($booking['start_date']) ?> → <?= e($booking['end_date']) ?></dd>
            <dt>Nuits</dt><dd><?= (int) $booking['nights'] ?></dd>
            <dt>Sous-total hébergement</dt><dd><?= money($booking['subtotal']) ?></dd>
            <dt>Frais de ménage</dt><dd><?= money($booking['cleaning_fee']) ?></dd>
            <dt>Total</dt><dd><strong><?= money($booking['total_price']) ?></strong></dd>
            <dt>Statut</dt><dd><?= status_label($booking['status']) ?></dd>
            <dt>Paiement</dt><dd><?= status_label($booking['payment_status']) ?></dd>
        </dl>
        <p class="notice">Paiement fictif de démonstration. Aucun montant réel ne sera débité.</p>
        <p><strong><?= e(config('academic_disclaimer')) ?></strong></p>
        <button class="button full" name="scenario" value="success" type="submit" data-track="booking_payment_test_success">Valider le paiement fictif</button>
        <button class="button ghost full" name="scenario" value="failure" type="submit" data-track="booking_payment_test_failure">Simuler un refus de paiement</button>
    </form>
</section>
