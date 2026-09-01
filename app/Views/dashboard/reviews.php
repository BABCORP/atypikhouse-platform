<?php require __DIR__ . '/_nav.php'; ?>
<?php
$reviewsByBooking = [];
foreach ($reviews ?? [] as $review) {
    $reviewsByBooking[(int) $review['booking_id']] = $review;
}
?>
<section class="section">
    <h1>Mes avis</h1>
    <p class="notice">Retrouvez ici vos logements réservés. Après un séjour terminé et payé fictivement, vous pouvez publier votre avis : il apparaît immédiatement sur la fiche logement.</p>
    <?php foreach (($bookings ?? []) as $booking): ?>
        <?php
        $review = $reviewsByBooking[(int) $booking['id']] ?? null;
        $hasStayEnded = (string) $booking['end_date'] <= date('Y-m-d');
        $displayStatus = (!$hasStayEnded && $booking['status'] === 'completed') ? 'confirmed' : (string) $booking['status'];
        ?>
        <article role="article" class="panel review-booking-card">
            <div class="section-heading">
                <div>
                    <p class="eyebrow">Réservation #<?= (int) $booking['id'] ?></p>
                    <h2><?= e($booking['title']) ?></h2>
                    <p><?= e($booking['city']) ?> · <?= e($booking['start_date']) ?> au <?= e($booking['end_date']) ?></p>
                </div>
                <span class="badge <?= e($displayStatus) ?>"><?= status_label($displayStatus) ?></span>
            </div>

            <?php if ($review): ?>
                <div class="review-card">
                    <p class="rating-summary"><?= rating_stars((int) $review['rating'], 'Note de votre avis') ?> <span><?= (int) $review['rating'] ?>/5 · <?= e(status_label($review['status'])) ?></span></p>
                    <p><?= e($review['comment']) ?></p>
                </div>
            <?php elseif ((int) ($booking['can_review'] ?? 0) === 1): ?>
                <form role="form" method="post" action="<?= url('/avis/' . $booking['id']) ?>" class="review-inline-form">
                    <?= csrf_field() ?>
                    <h3>Partager mon expérience</h3>
                    <label>Note
                        <select name="rating" required>
                            <option value="5">5/5</option>
                            <option value="4">4/5</option>
                            <option value="3">3/5</option>
                            <option value="2">2/5</option>
                            <option value="1">1/5</option>
                        </select>
                    </label>
                    <label>Commentaire
                        <textarea required name="comment" maxlength="1200" placeholder="Décrivez votre séjour, l’accueil, le logement et l’expérience vécue."></textarea>
                    </label>
                    <button class="button compact" type="submit" data-track="review_submit">Publier mon avis</button>
                </form>
            <?php elseif ($booking['payment_status'] !== 'test_paid'): ?>
                <p class="empty-state">L’avis sera disponible après validation du paiement fictif.</p>
            <?php elseif (!$hasStayEnded): ?>
                <p class="empty-state">Ce séjour est à venir. Vous pourrez publier un avis après la fin du séjour, à partir du <?= e($booking['end_date']) ?>.</p>
            <?php else: ?>
                <p class="empty-state">Aucun avis disponible pour cette réservation pour le moment.</p>
            <?php endif; ?>
        </article>
    <?php endforeach; ?>
    <?php if (empty($bookings)): ?><p class="empty-state">Vous n’avez pas encore de réservation liée à un avis.</p><?php endif; ?>
</section>
