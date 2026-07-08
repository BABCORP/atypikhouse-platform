<?php require __DIR__ . '/_nav.php'; ?>
<section class="section narrow">
    <form class="panel" method="post">
        <?= csrf_field() ?>
        <p class="eyebrow">Administration logement</p>
        <h1>Modifier <?= e($property['title']) ?></h1>
        <label>Titre<input required name="title" value="<?= e($property['title']) ?>"></label>
        <label>Type
            <select name="type">
                <?php foreach (['treehouse', 'yurt', 'floating_cabin', 'tiny_house', 'dome', 'other'] as $type): ?>
                    <option value="<?= $type ?>" <?= $property['type'] === $type ? 'selected' : '' ?>><?= property_type_label($type) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Description courte<input required name="short_description" value="<?= e($property['short_description']) ?>"></label>
        <label>Description détaillée<textarea required name="long_description"><?= e($property['long_description']) ?></textarea></label>
        <div class="form-grid two">
            <label>Ville<input required name="city" value="<?= e($property['city']) ?>"></label>
            <label>Région<input required name="region" value="<?= e($property['region']) ?>"></label>
            <label>Code postal<input name="postal_code" value="<?= e($property['postal_code']) ?>"></label>
            <label>Pays<input name="country" value="<?= e($property['country']) ?>"></label>
            <label>Capacité<input required type="number" min="1" name="capacity" value="<?= (int) $property['capacity'] ?>"></label>
            <label>Chambres<input type="number" min="1" name="bedrooms" value="<?= (int) $property['bedrooms'] ?>"></label>
            <label>Lits<input type="number" min="1" name="beds" value="<?= (int) $property['beds'] ?>"></label>
            <label>Salles d’eau<input type="number" min="1" name="bathrooms" value="<?= (int) $property['bathrooms'] ?>"></label>
            <label>Prix / nuit<input required type="number" step="0.01" min="1" name="price_per_night" value="<?= e($property['price_per_night']) ?>"></label>
            <label>Frais de ménage<input type="number" step="0.01" min="0" name="cleaning_fee" value="<?= e($property['cleaning_fee']) ?>"></label>
            <label>Éco-score<input type="number" min="1" max="5" name="eco_score" value="<?= (int) $property['eco_score'] ?>"></label>
        </div>
        <label>Équipements<textarea name="amenities"><?= e($amenities) ?></textarea></label>
        <div class="actions-row">
            <button class="button" type="submit">Enregistrer</button>
            <a class="button ghost" href="<?= url('/admin/logements/' . $property['id']) ?>">Annuler</a>
        </div>
    </form>
</section>
