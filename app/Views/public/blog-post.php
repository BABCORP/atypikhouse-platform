<article role="article" class="section article">
    <p class="eyebrow">Blog AtypikHouse</p>
    <h1><?= e($post['title']) ?></h1>
    <p class="lead"><?= e($post['excerpt']) ?></p>
    <img class="article-cover" loading="lazy" src="<?= image_url($post['featured_image']) ?>" alt="Image d’illustration de l’article <?= e($post['title']) ?>">
    <div><?= nl2br(e($post['content'])) ?></div>
    <div class="actions article-links">
        <a class="button" href="<?= url('/hebergements') ?>" data-track="cta_click">Explorer les hébergements</a>
        <a class="button ghost" href="<?= url('/contact') ?>" data-track="cta_click">Poser une question</a>
    </div>
</article>
