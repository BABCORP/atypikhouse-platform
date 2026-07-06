<?php require __DIR__ . '/_nav.php'; ?>
<section class="section form-layout narrow">
    <form class="panel" method="post">
        <?= csrf_field() ?><h1>Modifier l’article</h1>
        <label>Titre<input required name="title" value="<?= e($post['title']) ?>"></label>
        <label>Extrait<input required name="excerpt" value="<?= e($post['excerpt']) ?>"></label>
        <label>Image mise en avant<input name="featured_image" value="<?= e($post['featured_image']) ?>"></label>
        <label>Contenu<textarea required name="content"><?= e($post['content']) ?></textarea></label>
        <label>Statut<select name="status"><option value="published" <?= $post['status'] === 'published' ? 'selected' : '' ?>>Publié</option><option value="draft" <?= $post['status'] === 'draft' ? 'selected' : '' ?>>Brouillon</option></select></label>
        <button class="button">Enregistrer</button>
    </form>
</section>
