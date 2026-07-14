<section class="section form-layout narrow">
    <form class="panel" method="post" data-track="booking_confirm">
        <h1>Paiement fictif de démonstration</h1>
        <?= csrf_field() ?>
        <p><strong><?= e(config('academic_disclaimer')) ?></strong></p>
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
        <p>Aucun numéro de carte réel n’est demandé. Choisissez simplement un scénario de démonstration.</p>
        <button class="button full" name="scenario" value="success" type="submit" data-track="booking_payment_test_success">Valider le paiement fictif</button>
        <button class="button secondary full" name="scenario" value="failure" type="submit" data-track="booking_payment_test_failure">Simuler un refus de paiement</button>
    </form>
</section>
