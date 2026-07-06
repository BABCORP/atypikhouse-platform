<?php require __DIR__ . '/_nav.php'; ?>
<?php $isEdit = !empty($property); ?>
<section class="section form-layout">
    <form class="panel" method="post" enctype="multipart/form-data" action="<?= $isEdit ? url('/proprietaire/logements/' . $property['id'] . '/modifier') : url('/proprietaire/logements/ajouter') ?>" data-track="property_submit">
        <?= csrf_field() ?><h1><?= e($title) ?></h1>
        <label>Titre<input required name="title" value="<?= e($property['title'] ?? '') ?>"></label>
        <label>Type<select name="type"><?php foreach (['treehouse'=>'Cabane','yurt'=>'Yourte','floating_cabin'=>'Cabane flottante','tiny_house'=>'Tiny house','dome'=>'Dôme','other'=>'Autre'] as $v=>$l): ?><option value="<?= $v ?>" <?= (($property['type'] ?? '')===$v)?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></label>
        <label>Description courte<input required name="short_description" value="<?= e($property['short_description'] ?? '') ?>"></label>
        <label>Description longue<textarea required name="long_description"><?= e($property['long_description'] ?? '') ?></textarea></label>
        <label>Adresse<input name="address" value="<?= e($property['address'] ?? '') ?>"></label>
        <label>Ville<input required name="city" value="<?= e($property['city'] ?? '') ?>"></label>
        <label>Code postal<input name="postal_code" value="<?= e($property['postal_code'] ?? '') ?>"></label>
        <label>Région<input required name="region" value="<?= e($property['region'] ?? '') ?>"></label>
        <label>Pays<input name="country" value="<?= e($property['country'] ?? 'France') ?>"></label>
        <div class="form-grid"><label>Capacité<input required type="number" name="capacity" min="1" value="<?= e($property['capacity'] ?? '2') ?>"></label><label>Chambres<input type="number" name="bedrooms" min="0" value="<?= e($property['bedrooms'] ?? '1') ?>"></label><label>Lits<input type="number" name="beds" min="1" value="<?= e($property['beds'] ?? '1') ?>"></label><label>Sdb<input type="number" name="bathrooms" min="0" value="<?= e($property['bathrooms'] ?? '1') ?>"></label></div>
        <div class="form-grid"><label>Prix/nuit<input required type="number" step="0.01" name="price_per_night" value="<?= e($property['price_per_night'] ?? '120') ?>"></label><label>Frais ménage<input type="number" step="0.01" name="cleaning_fee" value="<?= e($property['cleaning_fee'] ?? '25') ?>"></label><label>Eco-score<input type="number" min="1" max="5" name="eco_score" value="<?= e($property['eco_score'] ?? '4') ?>"></label></div>
        <label>Équipements, séparés par des virgules<input name="amenities" placeholder="Spa, Wifi, Petit-déjeuner" value="<?= e(isset($amenities) ? implode(', ', $amenities) : '') ?>"></label>
        <label><?= $isEdit ? 'Ajouter une nouvelle image' : 'Image principale' ?> (JPG, PNG ou WEBP, 5 Mo max.)<input type="file" name="property_image" accept="image/jpeg,image/png,image/webp"></label>
        <label>Texte alternatif de l’image<input name="image_alt" placeholder="Cabane perchée entourée de chênes"></label>
        <p class="notice">Si aucune image n’est importée, un visuel de remplacement accessible est utilisé.</p>
        <button class="button" type="submit">Enregistrer</button>
    </form>
    <?php if ($isEdit): ?>
        <aside class="panel gallery-manager" aria-labelledby="gallery-title">
            <h2 id="gallery-title">Galerie du logement</h2>
            <p class="notice">Chaque image doit avoir un texte alternatif clair pour l’accessibilité et le SEO.</p>
            <div class="owner-gallery-grid">
                <?php foreach (($images ?? []) as $image): ?>
                    <article class="owner-image-card">
                        <img src="<?= image_url($image['image_path']) ?>" alt="<?= e($image['alt_text']) ?>" loading="lazy">
                        <span class="badge <?= (int) $image['is_main'] === 1 ? 'published' : '' ?>"><?= (int) $image['is_main'] === 1 ? 'Image principale' : 'Image secondaire' ?></span>
                        <form method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/alt') ?>">
                            <?= csrf_field() ?>
                            <label>Texte alternatif<input name="alt_text" value="<?= e($image['alt_text']) ?>" required></label>
                            <button class="button compact" type="submit">Mettre à jour</button>
                        </form>
                        <div class="actions-row">
                            <?php if ((int) $image['is_main'] !== 1): ?>
                                <form method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/principale') ?>"><?= csrf_field() ?><button class="button ghost compact" type="submit">Définir principale</button></form>
                            <?php endif; ?>
                            <form method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/supprimer') ?>" data-confirm="Supprimer cette image du logement ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </aside>
    <?php endif; ?>
</section>
