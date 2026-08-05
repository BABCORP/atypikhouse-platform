<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div><p class="eyebrow">Éditorial</p><h1>Gestion du blog</h1></div>
    </div>
    <div class="dashboard-grid two">
        <form role="form" class="panel" method="post">
            <?= csrf_field() ?>
            <h2>Créer un article</h2>
            <label>Titre<input required name="title" placeholder="Ex. 5 idées pour un week-end insolite"></label>
            <label>Extrait<input required name="excerpt" placeholder="Résumé court visible dans la liste des articles"></label>
            <label>Image mise en avant<input name="featured_image" placeholder="Ex. assets/img/blog/sejour-nature.jpg" value="assets/img/properties/default-placeholder.svg"></label>
            <label>Contenu<textarea required name="content" placeholder="Rédigez ici le contenu complet de l’article."></textarea></label>
            <label>Statut
                <select name="status"><option value="published">Publié</option><option value="draft">Brouillon</option></select>
            </label>
            <button class="button" type="submit">Créer l’article</button>
        </form>
        <div class="blog-admin-list">
            <?php foreach ($posts as $post): ?>
                <article role="article" class="panel">
                    <p class="eyebrow"><?= e($post['author_email'] ?? 'AtypikHouse') ?></p>
                    <h2><?= e($post['title']) ?></h2>
                    <p><span class="badge <?= e($post['status']) ?>"><?= status_label($post['status']) ?></span> · <?= e($post['created_at']) ?></p>
                    <p><?= e($post['excerpt']) ?></p>
                    <div class="actions-row">
                        <a class="button ghost compact" href="<?= url('/admin/blog/' . $post['id'] . '/modifier') ?>">Modifier</a>
                        <?php if ($post['status'] === 'published'): ?>
                            <form role="form" class="inline-form" method="post" action="<?= url('/admin/blog/' . $post['id'] . '/depublier') ?>"><?= csrf_field() ?><button class="button ghost compact" type="submit">Dépublier</button></form>
                        <?php else: ?>
                            <form role="form" class="inline-form" method="post" action="<?= url('/admin/blog/' . $post['id'] . '/publier') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Publier</button></form>
                        <?php endif; ?>
                        <form role="form" class="inline-form" method="post" action="<?= url('/admin/blog/' . $post['id'] . '/supprimer') ?>" data-confirm="Supprimer définitivement cet article de blog ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
