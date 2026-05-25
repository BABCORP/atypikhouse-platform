<section class="page-hero compact"><h1>Blog</h1><p>Conseils et inspirations pour imaginer un week-end insolite.</p></section>
<section class="section grid cards">
    <?php foreach ($posts as $post): ?>
        <article class="card blog-card"><img loading="lazy" src="<?= image_url($post['featured_image']) ?>" alt=""><div class="card-body"><p class="eyebrow"><?= e(date('d/m/Y', strtotime($post['created_at']))) ?></p><h2><a href="<?= url('/blog/' . $post['slug']) ?>"><?= e($post['title']) ?></a></h2><p><?= e($post['excerpt']) ?></p><a class="text-link" href="<?= url('/blog/' . $post['slug']) ?>">Lire l’article</a></div></article>
    <?php endforeach; ?>
</section>
