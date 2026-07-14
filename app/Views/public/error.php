<section class="page-hero compact error-hero">
    <p class="eyebrow">Erreur <?= (int) ($code ?? 404) ?></p>
    <h1><?= e($heading ?? 'Page indisponible') ?></h1>
    <p><?= e($body ?? 'La page demandée n’est pas disponible sur AtypikHouse.') ?></p>
    <p class="notice"><?= e(config('academic_disclaimer')) ?></p>
    <div class="actions">
        <a class="button" href="<?= url('/') ?>">Retour à l’accueil</a>
        <a class="button ghost" href="<?= url('/hebergements') ?>">Voir les hébergements</a>
        <a class="button ghost" href="<?= url('/contact') ?>">Contacter l’équipe</a>
    </div>
</section>
