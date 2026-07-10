<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Sélection personnelle</p>
            <h1>Mes favoris</h1>
        </div>
        <a class="button ghost" href="<?= url('/hebergements') ?>">Explorer les hébergements</a>
    </div>
    <?php if ($properties): ?>
        <div class="grid cards">
            <?php foreach ($properties as $property): require dirname(__DIR__) . '/public/_property-card.php'; endforeach; ?>
        </div>
    <?php else: ?>
        <article class="panel empty-state">
            <h2>Aucun favori pour le moment</h2>
            <p>Ajoutez un logement avec le cœur pour le retrouver ici pendant votre navigation.</p>
            <a class="button" href="<?= url('/hebergements') ?>">Découvrir les hébergements</a>
        </article>
    <?php endif; ?>
</section>
