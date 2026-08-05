<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Modération</p><h1>Avis voyageurs</h1></div></div>
    <form role="form" class="filters panel" method="get">
        <label>Statut
            <select name="status">
                <option value="">Tous</option>
                <?php foreach (['pending', 'published', 'rejected'] as $status): ?>
                    <option value="<?= $status ?>" <?= input('status') === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Note
            <select name="rating">
                <option value="">Toutes</option>
                <?php foreach ([5, 4, 3, 2, 1] as $rating): ?>
                    <option value="<?= $rating ?>" <?= (string) input('rating') === (string) $rating ? 'selected' : '' ?>><?= $rating ?>/5</option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Logement<input name="property" value="<?= e(input('property', '')) ?>" placeholder="Titre du logement"></label>
        <label>Auteur<input name="author" value="<?= e(input('author', '')) ?>" placeholder="Email"></label>
        <button class="button compact" type="submit">Filtrer</button>
    </form>
    <div class="dashboard-grid two">
        <?php foreach ($reviews as $review): ?>
            <article role="article" class="panel">
                <h2><?= e($review['title']) ?></h2>
                <p><?= (int) $review['rating'] ?>/5 · <?= e($review['email']) ?> · <span class="badge <?= e($review['status']) ?>"><?= status_label($review['status']) ?></span></p>
                <p><?= e($review['comment']) ?></p>
                <div class="actions-row">
                    <form role="form" class="inline-form" method="post" action="<?= url('/admin/avis/' . $review['id'] . '/statut') ?>"><?= csrf_field() ?><button class="button compact" name="status" value="published" type="submit">Publier</button><button class="button ghost compact" name="status" value="rejected" type="submit">Rejeter</button></form>
                    <form role="form" class="inline-form" method="post" action="<?= url('/admin/avis/' . $review['id'] . '/supprimer') ?>" data-confirm="Supprimer définitivement cet avis ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($reviews)): ?><p class="empty-state">Aucun avis ne correspond aux critères.</p><?php endif; ?>
    </div>
</section>
