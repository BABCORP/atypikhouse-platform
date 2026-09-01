<section class="page-hero compact">
    <p class="eyebrow">AtypikHouse</p>
    <h1><?= e($heading) ?></h1>
    <p><?= e($body) ?></p>
    <?php if (!empty($cta)): ?><a class="button" href="<?= url($cta['url']) ?>" data-track="cta_click"><?= e($cta['label']) ?></a><?php endif; ?>
</section>
