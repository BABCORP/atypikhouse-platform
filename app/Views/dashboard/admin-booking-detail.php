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
                <dt>Propriétaire</dt><dd><?= e($booking['owner_first_name'] . ' ' . $booking['owner_last_name'] . ' · ' . $booking['owner_email']) ?></dd>
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
                <dt>Prix indicatif / nuit</dt><dd><?= money($booking['price_per_night']) ?></dd>
                <dt>Total simulé</dt><dd><strong><?= money($booking['total_price']) ?></strong></dd>
                <dt>Transaction test</dt><dd><?= e($booking['test_transaction_id'] ?: 'Aucune') ?></dd>
                <dt>Créée le</dt><dd><?= e($booking['created_at']) ?></dd>
                <dt>Mise à jour le</dt><dd><?= e($booking['updated_at']) ?></dd>
            </dl>
        </article>
    </div>
    <article class="panel availability-calendar-panel">
        <div class="calendar-toolbar">
            <button class="button ghost compact" type="button" data-calendar-prev>Mois précédent</button>
            <h2 data-calendar-title>Calendrier du logement</h2>
            <button class="button ghost compact" type="button" data-calendar-next>Mois suivant</button>
        </div>
        <div class="calendar-legend" aria-label="Légende des disponibilités">
            <span><i class="legend-dot available"></i>Disponible</span>
            <span><i class="legend-dot unavailable"></i>Indisponible</span>
            <span><i class="legend-dot override"></i>Prix spécifique</span>
            <span><i class="legend-dot booked"></i>Réservé</span>
        </div>
        <div class="availability-calendar" data-availability-calendar data-calendar-payload="<?= e(json_encode($calendarData ?? [], JSON_UNESCAPED_UNICODE)) ?>" aria-live="polite"></div>
        <p class="notice">Vue administrateur : les dates bloquées proviennent des réservations en attente de validation, confirmées ou terminées.</p>
    </article>
    <form class="panel inline-admin-form" method="post" action="<?= url('/admin/reservations/' . $booking['id'] . '/statut') ?>">
        <?= csrf_field() ?>
        <label>Statut
            <select name="status">
                <?php foreach (['pending_admin', 'confirmed', 'cancelled', 'completed'] as $status): ?>
                    <option value="<?= $status ?>" <?= $booking['status'] === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button" type="submit">Mettre à jour</button>
        <p><span class="badge <?= e($booking['status']) ?>"><?= status_label($booking['status']) ?></span> <span class="badge"><?= status_label($booking['payment_status']) ?></span></p>
    </form>
    <div class="actions-row">
        <?php if ($booking['status'] === 'pending_admin'): ?>
            <form method="post" action="<?= url('/admin/reservations/' . $booking['id'] . '/confirmer') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Confirmer</button></form>
        <?php endif; ?>
        <form method="post" action="<?= url('/admin/reservations/' . $booking['id'] . '/terminer') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Marquer terminée</button></form>
        <form method="post" action="<?= url('/admin/reservations/' . $booking['id'] . '/annuler') ?>" data-confirm="Annuler cette réservation fictive ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Annuler</button></form>
    </div>
</section>
