<section class="page-hero compact"><h1>Blog</h1><p>Conseils et inspirations pour imaginer un week-end insolite.</p></section>
<section class="section grid cards">
    <?php foreach ($posts as $post): ?>
        <article class="card"><div class="card-body"><p class="eyebrow"><?= e(date('d/m/Y', strtotime($post['created_at']))) ?></p><h2><a href="<?= url('/blog/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h2><p><?= e($post['excerpt']) ?></p></div></article>
    <?php endforeach; ?>
</section>
