<?php require __DIR__ . '/_nav.php'; ?>
<section class="section article">
    <h1>Réservation #<?= (int) $booking['id'] ?></h1>
    <p><strong><?= e($booking['title']) ?></strong> à <?= e($booking['city']) ?></p>
    <p>Dates : <?= e($booking['start_date']) ?> au <?= e($booking['end_date']) ?> · <?= (int) $booking['nights'] ?> nuit(s)</p>
    <p>Total fictif : <?= money($booking['total_price']) ?> · Paiement : <?= e($booking['payment_status']) ?></p>
    <p class="notice"><?= e(config('academic_disclaimer')) ?></p>
    <?php if ($canReview): ?>
        <form class="panel" method="post" action="<?= url('/avis/' . $booking['id']) ?>">
            <?= csrf_field() ?><h2>Déposer un avis</h2>
            <label>Note<select name="rating"><option>5</option><option>4</option><option>3</option><option>2</option><option>1</option></select></label>
            <label>Commentaire<textarea required name="comment"></textarea></label>
            <button class="button" type="submit">Envoyer l’avis</button>
        </form>
    <?php endif; ?>
</section>
