<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Modération</p><h1>Avis voyageurs</h1></div></div>
    <div class="dashboard-grid two">
        <?php foreach ($reviews as $review): ?>
            <article class="panel">
                <h2><?= e($review['title']) ?></h2>
                <p><?= (int) $review['rating'] ?>/5 · <?= e($review['email']) ?> · <span class="badge <?= e($review['status']) ?>"><?= status_label($review['status']) ?></span></p>
                <p><?= e($review['comment']) ?></p>
                <div class="actions-row">
                    <form class="inline-form" method="post" action="<?= url('/admin/avis/' . $review['id'] . '/statut') ?>"><?= csrf_field() ?><button class="button compact" name="status" value="published" type="submit">Publier</button><button class="button ghost compact" name="status" value="rejected" type="submit">Rejeter</button></form>
                    <form class="inline-form" method="post" action="<?= url('/admin/avis/' . $review['id'] . '/supprimer') ?>"><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
