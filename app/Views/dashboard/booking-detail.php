<?php require __DIR__ . '/_nav.php'; ?>
<section class="section article">
    <h1>Réservation #<?= (int) $booking['id'] ?></h1>
    <p><strong><?= e($booking['title']) ?></strong> à <?= e($booking['city']) ?></p>
    <p>Dates : <?= e($booking['start_date']) ?> au <?= e($booking['end_date']) ?> · <?= (int) $booking['nights'] ?> nuit(s)</p>
    <dl class="detail-list panel">
        <dt>Sous-total hébergement</dt><dd><?= money($booking['subtotal']) ?></dd>
        <dt>Frais de ménage</dt><dd><?= money($booking['cleaning_fee']) ?></dd>
        <dt>Total fictif</dt><dd><strong><?= money($booking['total_price']) ?></strong></dd>
        <dt>Statut</dt><dd><span class="badge <?= e($booking['status']) ?>"><?= status_label($booking['status']) ?></span></dd>
        <dt>Paiement</dt><dd><span class="badge muted"><?= status_label($booking['payment_status']) ?></span></dd>
    </dl>
    <p class="notice"><?= e(config('academic_disclaimer')) ?></p>
    <?php if ($booking['status'] === 'pending_admin'): ?>
        <p class="notice">Cette réservation attend la validation de l’administrateur avant le paiement fictif.</p>
    <?php elseif ($booking['status'] === 'pending_payment' && in_array($booking['payment_status'], ['not_paid', 'test_failed'], true)): ?>
        <p class="notice">Votre demande a été validée par l’administrateur. Le paiement fictif doit maintenant être effectué pour confirmer la réservation.</p>
        <p><a class="button" href="<?= url('/paiement/' . $booking['id']) ?>">Poursuivre vers le paiement fictif</a></p>
    <?php endif; ?>
    <article class="panel invoice-print" id="facture-fictive">
        <div class="section-heading">
            <div>
                <p class="eyebrow">Reçu fictif</p>
                <h2>Facture fictive #<?= (int) $booking['id'] ?></h2>
            </div>
            <button class="button compact no-print" type="button" data-print-target="facture-fictive">Imprimer</button>
        </div>
        <p><strong>Document fictif — aucune transaction réelle.</strong></p>
        <dl class="detail-list">
            <dt>Locataire</dt><dd><?= e(($booking['tenant_first_name'] ?? '') . ' ' . ($booking['tenant_last_name'] ?? '')) ?> · <?= e($booking['tenant_email'] ?? '') ?></dd>
            <dt>Logement</dt><dd><?= e($booking['title']) ?>, <?= e($booking['city']) ?></dd>
            <dt>Dates</dt><dd><?= e($booking['start_date']) ?> → <?= e($booking['end_date']) ?></dd>
            <dt>Nuits</dt><dd><?= (int) $booking['nights'] ?></dd>
            <dt>Voyageurs</dt><dd><?= (int) $booking['guests_count'] ?></dd>
            <dt>Sous-total</dt><dd><?= money($booking['subtotal']) ?></dd>
            <dt>Frais de ménage</dt><dd><?= money($booking['cleaning_fee']) ?></dd>
            <dt>Total</dt><dd><strong><?= money($booking['total_price']) ?></strong></dd>
            <dt>Paiement</dt><dd><?= status_label($booking['payment_status']) ?></dd>
            <dt>Transaction fictive</dt><dd><?= e($booking['test_transaction_id'] ?? 'Aucune') ?></dd>
        </dl>
    </article>
    <?php if ($canReview): ?>
        <form class="panel" method="post" action="<?= url('/avis/' . $booking['id']) ?>">
            <?= csrf_field() ?><h2>Déposer un avis</h2>
            <label>Note<select name="rating"><option>5</option><option>4</option><option>3</option><option>2</option><option>1</option></select></label>
            <label>Commentaire<textarea required name="comment"></textarea></label>
            <button class="button" type="submit" data-track="review_submit">Envoyer l’avis</button>
        </form>
    <?php endif; ?>
</section>
