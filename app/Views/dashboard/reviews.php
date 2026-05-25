<?php require __DIR__ . '/_nav.php'; ?>
<section class="section"><h1>Mes avis</h1><?php foreach ($reviews as $review): ?><article class="panel"><strong><?= e($review['title']) ?></strong><p><?= (int)$review['rating'] ?>/5 · <?= e($review['status']) ?></p><p><?= e($review['comment']) ?></p></article><?php endforeach; ?></section>
