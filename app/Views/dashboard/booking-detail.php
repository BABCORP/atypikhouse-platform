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
        <p class="notice">Cette réservation peut maintenant être poursuivie vers le paiement fictif.</p>
        <p><a class="button" href="<?= url('/paiement/' . $booking['id']) ?>">Poursuivre vers le paiement fictif</a></p>
    <?php elseif ($booking['status'] === 'pending_payment' && in_array($booking['payment_status'], ['not_paid', 'test_failed'], true)): ?>
        <p class="notice">Le paiement fictif doit être effectué pour confirmer la réservation.</p>
        <p><a class="button" href="<?= url('/paiement/' . $booking['id']) ?>">Poursuivre vers le paiement fictif</a></p>
    <?php endif; ?>
    <article role="article" class="panel invoice-print" id="facture-fictive">
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
            <dt>Transaction fictive</dt><dd class="transaction-reference"><?= e($booking['test_transaction_id'] ?? 'Aucune') ?></dd>
        </dl>
    </article>
    <?php if ($canReview): ?>
        <form role="form" class="panel" method="post" action="<?= url('/avis/' . $booking['id']) ?>" id="deposer-avis">
            <?= csrf_field() ?><h2>Déposer un avis</h2>
            <p class="notice">Votre séjour est terminé. Vous pouvez partager votre expérience ; l’avis sera publié immédiatement.</p>
            <label>Note<select name="rating" required><option value="5">5/5</option><option value="4">4/5</option><option value="3">3/5</option><option value="2">2/5</option><option value="1">1/5</option></select></label>
            <label>Commentaire<textarea required name="comment" maxlength="1200" placeholder="Décrivez votre séjour, l’accueil, le logement et l’expérience vécue."></textarea></label>
            <button class="button" type="submit" data-track="review_submit">Publier mon avis</button>
        </form>
    <?php elseif (!empty($booking['review_id'])): ?>
        <div class="panel" id="deposer-avis">
            <h2>Avis déjà transmis</h2>
            <p>Votre avis est publié sur la fiche logement.</p>
        </div>
    <?php elseif (in_array($booking['status'], ['confirmed', 'completed'], true) && $booking['payment_status'] === 'test_paid' && $booking['end_date'] > date('Y-m-d')): ?>
        <div class="panel" id="deposer-avis">
            <h2>Avis après séjour</h2>
            <p>Vous pourrez déposer un avis après la fin du séjour, à partir du <?= e($booking['end_date']) ?>.</p>
        </div>
    <?php endif; ?>
</section>
