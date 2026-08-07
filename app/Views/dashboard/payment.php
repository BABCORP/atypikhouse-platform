<section class="section form-layout narrow">
    <div class="panel payment-demo-card">
        <p class="eyebrow">Simulation de paiement</p>
        <h1>Paiement fictif de démonstration</h1>
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
        <?php if (!empty($stripeReady)): ?>
            <form role="form" class="stacked-actions" method="post" action="<?= url('/paiement/' . $booking['id'] . '/stripe') ?>" data-track="booking_payment_stripe_test">
                <?= csrf_field() ?>
                <button class="button full" type="submit" data-loading-text="Redirection vers Stripe...">Payer avec Stripe en mode test</button>
            </form>
            <p class="form-help">Utilisez la carte test Stripe 4242 4242 4242 4242, une date future et n’importe quel CVC.</p>
        <?php else: ?>
            <p class="notice">Stripe test n’est pas encore configuré sur cet environnement. La simulation interne reste disponible pour la démonstration.</p>
        <?php endif; ?>
        <form role="form" class="stacked-actions" method="post" data-track="booking_confirm">
            <?= csrf_field() ?>
            <button class="button full" name="scenario" value="success" type="submit" data-track="booking_payment_test_success">Valider le paiement fictif sans Stripe</button>
            <button class="button ghost full" name="scenario" value="failure" type="submit" data-track="booking_payment_test_failure">Simuler un refus de paiement</button>
        </form>
    </div>
</section>
