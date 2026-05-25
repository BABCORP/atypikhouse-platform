<article class="section article">
    <p class="eyebrow">Blog AtypikHouse</p>
    <h1><?= e($post['title']) ?></h1>
    <p class="lead"><?= e($post['excerpt']) ?></p>
    <img class="article-cover" src="<?= image_url($post['featured_image']) ?>" alt="">
    <div><?= nl2br(e($post['content'])) ?></div>
</article>
