<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <h1>Mes logements</h1>
    <a class="button" href="<?= url('/proprietaire/logements/ajouter') ?>">Ajouter un logement</a>
    <?php require __DIR__ . '/_property-table.php'; ?>
</section>
