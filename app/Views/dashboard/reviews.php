<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1>Mes avis</h1>
    <?php foreach ($reviews as $review): ?>
        <article class="panel">
            <strong><?= e($review['title']) ?></strong>
            <p class="rating-summary"><?= rating_stars((int) $review['rating'], 'Note de votre avis') ?> <span><?= (int)$review['rating'] ?>/5 · <?= e(status_label($review['status'])) ?></span></p>
            <p><?= e($review['comment']) ?></p>
        </article>
    <?php endforeach; ?>
    <?php if (!$reviews): ?><p class="empty-state">Vous n’avez pas encore déposé d’avis.</p><?php endif; ?>
</section>
