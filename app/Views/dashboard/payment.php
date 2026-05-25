<section class="section form-layout narrow">
    <form class="panel" method="post" data-track="booking_confirm">
        <h1>Paiement fictif</h1>
        <?= csrf_field() ?>
        <p><strong><?= e(config('academic_disclaimer')) ?></strong></p>
        <p>Réservation #<?= (int)$booking['id'] ?> · Total test : <?= money($booking['total_price']) ?></p>
        <p>Aucun numéro de carte réel n’est demandé. Choisissez simplement un scénario de démonstration.</p>
        <button class="button full" name="scenario" value="success" type="submit">Simuler le paiement réussi</button>
        <button class="button secondary full" name="scenario" value="failure" type="submit">Simuler un échec</button>
    </form>
</section>
