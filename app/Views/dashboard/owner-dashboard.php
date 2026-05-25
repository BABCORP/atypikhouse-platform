<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1>Espace propriétaire</h1>
    <div class="stats">
        <article><strong><?= count($properties) ?></strong><span>logements</span></article>
        <article><strong><?= count(array_filter($properties, fn($p) => $p['status']==='published')) ?></strong><span>publiés</span></article>
        <article><strong><?= count(array_filter($properties, fn($p) => $p['status']==='pending')) ?></strong><span>en attente</span></article>
        <article><strong><?= money(array_sum(array_map(fn($b) => $b['payment_status']==='test_paid' ? (float)$b['total_price'] : 0, $bookings))) ?></strong><span>revenu simulé</span></article>
    </div>
    <a class="button" href="<?= url('/proprietaire/logements/ajouter') ?>">Ajouter un logement</a>
</section>
