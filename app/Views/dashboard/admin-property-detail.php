<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Validation logement</p>
            <h1><?= e($property['title']) ?></h1>
            <p><?= e($property['city'] . ' · ' . $property['region']) ?> · <span class="badge <?= e($property['status']) ?>"><?= status_label($property['status']) ?></span></p>
        </div>
        <div class="actions-row">
            <a class="button ghost" href="<?= url('/admin/logements') ?>">Retour</a>
            <a class="button" href="<?= url('/admin/logements/' . $property['id'] . '/modifier') ?>">Modifier</a>
        </div>
    </div>
    <div class="dashboard-grid two">
        <article role="article" class="panel">
            <h2>Informations</h2>
            <dl class="detail-list">
                <dt>Propriétaire</dt><dd><?= e(($property['company_name'] ?: $property['owner_first_name'] . ' ' . $property['owner_last_name']) . ' · ' . $property['owner_email']) ?></dd>
                <dt>Type</dt><dd><?= property_type_label($property['type']) ?></dd>
                <dt>Capacité</dt><dd><?= (int) $property['capacity'] ?> voyageur(s), <?= (int) $property['bedrooms'] ?> chambre(s)</dd>
                <dt>Prix</dt><dd><?= money($property['price_per_night']) ?> / nuit · ménage <?= money($property['cleaning_fee']) ?></dd>
                <dt>Éco-score</dt><dd><?= (int) $property['eco_score'] ?>/5</dd>
                <dt>Créé le</dt><dd><?= e($property['created_at']) ?></dd>
                <dt>Mis à jour le</dt><dd><?= e($property['updated_at']) ?></dd>
            </dl>
        </article>
        <article role="article" class="panel">
            <h2>Actions admin</h2>
            <?php if (($bookingCount ?? 0) > 0): ?>
                <p class="notice">Attention : ce logement possède <?= (int) $bookingCount ?> réservation(s) existante(s). La pause ou la suppression logique conserve l’historique admin.</p>
            <?php endif; ?>
            <div class="actions-row">
                <?php if (in_array($property['status'], ['pending', 'rejected', 'draft'], true)): ?>
                    <form role="form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/approuver') ?>"><?= csrf_field() ?><button class="button" type="submit">Approuver et publier</button></form>
                <?php endif; ?>
                <?php if ($property['status'] === 'pending'): ?>
                    <form role="form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/refuser') ?>" data-confirm="Refuser ce logement ?"><?= csrf_field() ?><button class="button ghost" type="submit">Refuser</button></form>
                <?php endif; ?>
                <?php if ($property['status'] === 'published'): ?>
                    <form role="form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/mettre-en-pause') ?>" data-confirm="Mettre ce logement en pause ? Les réservations existantes seront conservées."><?= csrf_field() ?><button class="button ghost" type="submit">Mettre en pause</button></form>
                <?php endif; ?>
                <?php if ($property['status'] === 'paused'): ?>
                    <form role="form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/reactiver') ?>"><?= csrf_field() ?><button class="button" type="submit">Réactiver</button></form>
                <?php endif; ?>
                <?php if ($property['status'] !== 'deleted'): ?>
                    <form role="form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/supprimer') ?>" data-confirm="Confirmer la suppression de ce logement ? Il ne sera plus visible publiquement, mais son historique sera conservé."><?= csrf_field() ?><button class="button danger" type="submit">Supprimer</button></form>
                <?php endif; ?>
            </div>
            <?php if ($property['status'] === 'published'): ?><a class="button ghost compact" href="<?= url('/hebergements/' . $property['slug']) ?>">Voir la fiche publique</a><?php endif; ?>
        </article>
    </div>
    <article role="article" class="panel">
        <h2>Description</h2>
        <p><strong><?= e($property['short_description']) ?></strong></p>
        <p><?= nl2br(e($property['long_description'])) ?></p>
        <p><strong>Équipements :</strong> <?= e(implode(', ', $amenities)) ?></p>
    </article>
    <div class="dashboard-grid two">
        <?php foreach ($images as $image): ?>
            <figure class="panel">
                <img class="property-admin-image" src="<?= image_url($image['image_path']) ?>" alt="<?= e($image['alt_text']) ?>">
                <figcaption><?= e($image['alt_text']) ?><?= (int) $image['is_main'] === 1 ? ' · image principale' : '' ?></figcaption>
            </figure>
        <?php endforeach; ?>
    </div>
</section>
