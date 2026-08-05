<?php require __DIR__ . '/_nav.php'; ?>
<?php $isEdit = !empty($property); ?>
<?php
$requiresValidation = $isEdit && in_array($property['status'] ?? '', ['published', 'paused'], true);
$selectedAmenities = $amenities ?? [];
$amenityOptions = [
    'Terrasse',
    'Parking',
    'Chauffage',
    'Cuisine équipée',
    'Wifi',
    'Bain nordique / spa',
    'Vue nature',
    'Vue eau',
    'Mobilier extérieur',
    'Poêle',
    'Observation des étoiles',
    'Espace détente',
    'Animaux acceptés',
    'Accessible PMR',
];
$customAmenities = array_values(array_diff($selectedAmenities, $amenityOptions));
?>
<section class="section form-layout">
    <form role="form" class="panel" method="post" enctype="multipart/form-data" action="<?= $isEdit ? url('/proprietaire/logements/' . $property['id'] . '/modifier') : url('/proprietaire/logements/ajouter') ?>" data-track="property_submit">
        <?= csrf_field() ?>
        <p class="eyebrow">Soumission hôte</p>
        <h1><?= e($title) ?></h1>
        <p class="notice"><?= $requiresValidation ? 'Les modifications envoyées seront vérifiées par l’équipe AtypikHouse avant publication. La fiche publique actuelle reste inchangée jusque-là.' : 'Votre logement sera soumis à validation par l’équipe AtypikHouse avant publication.' ?></p>
        <?php if (!empty($pendingChange)): ?>
            <p class="notice">Une demande de modification est déjà en cours de validation. Un nouvel envoi remplacera cette demande en attente.</p>
        <?php endif; ?>

        <fieldset class="form-section">
            <legend>Informations principales</legend>
            <label>Titre du logement<input required name="title" placeholder="Cabane flottante sur l’étang" value="<?= e($property['title'] ?? '') ?>"></label>
            <label>Type d’hébergement<select name="type" required><?php foreach (['treehouse'=>'Cabane','yurt'=>'Yourte','floating_cabin'=>'Cabane flottante','tiny_house'=>'Tiny house','dome'=>'Dôme','other'=>'Autre'] as $v=>$l): ?><option value="<?= $v ?>" <?= (($property['type'] ?? '')===$v)?'selected':'' ?>><?= $l ?></option><?php endforeach; ?></select></label>
            <label>Description courte<input required name="short_description" maxlength="255" placeholder="Une parenthèse nature, calme et insolite." value="<?= e($property['short_description'] ?? '') ?>"></label>
            <label>Description détaillée<textarea required name="long_description" rows="10" placeholder="Décrivez l’ambiance, le confort, l’environnement, l’expérience proposée et les règles importantes du logement."><?= e($property['long_description'] ?? '') ?></textarea></label>
        </fieldset>

        <fieldset class="form-section">
            <legend>Localisation et environnement</legend>
            <label>Adresse ou lieu fictif<input name="address" placeholder="Chemin du bois, accès par parking privé" value="<?= e($property['address'] ?? '') ?>"></label>
            <div class="form-grid">
                <label>Ville<input required name="city" value="<?= e($property['city'] ?? '') ?>"></label>
                <label>Code postal<input name="postal_code" value="<?= e($property['postal_code'] ?? '') ?>"></label>
                <label>Région<input required name="region" value="<?= e($property['region'] ?? '') ?>"></label>
                <label>Pays<input name="country" value="<?= e($property['country'] ?? 'France') ?>"></label>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Capacité et confort</legend>
            <div class="form-grid">
                <label>Voyageurs<input required type="number" name="capacity" min="1" value="<?= e($property['capacity'] ?? '2') ?>"></label>
                <label>Chambres<input type="number" name="bedrooms" min="0" value="<?= e($property['bedrooms'] ?? '1') ?>"></label>
                <label>Lits<input type="number" name="beds" min="1" value="<?= e($property['beds'] ?? '1') ?>"></label>
                <label>Salles de bain<input type="number" name="bathrooms" min="0" value="<?= e($property['bathrooms'] ?? '1') ?>"></label>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Tarification</legend>
            <div class="form-grid">
                <label>Prix par nuit<input required type="number" min="1" step="0.01" name="price_per_night" value="<?= e($property['price_per_night'] ?? '120') ?>"></label>
                <label>Frais de ménage<input type="number" min="0" step="0.01" name="cleaning_fee" value="<?= e($property['cleaning_fee'] ?? '25') ?>"></label>
                <label>Eco-score<input type="number" min="1" max="5" name="eco_score" value="<?= e($property['eco_score'] ?? '4') ?>"></label>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Équipements</legend>
            <div class="checkbox-grid">
                <?php foreach ($amenityOptions as $amenity): ?>
                    <label class="checkbox"><input type="checkbox" name="amenities[]" value="<?= e($amenity) ?>" <?= in_array($amenity, $selectedAmenities, true) ? 'checked' : '' ?>><?= e($amenity) ?></label>
                <?php endforeach; ?>
                <label class="checkbox"><input type="checkbox" data-other-amenities-toggle <?= $customAmenities ? 'checked' : '' ?>>Autre</label>
            </div>
            <div class="other-amenities-field" data-other-amenities-field>
                <label>Autres équipements ou précisions
                    <textarea name="other_amenities" maxlength="500" placeholder="Ex. hamac, brasero, jeux de société, barbecue, borne de recharge…"><?= e(implode(', ', $customAmenities)) ?></textarea>
                </label>
            </div>
        </fieldset>

        <fieldset class="form-section">
            <legend>Images du logement</legend>
            <div class="upload-block">
                <h2>Image principale</h2>
                <p class="form-help">Cette image sera affichée en premier sur la fiche logement et dans le catalogue.</p>
                <label>Image principale (JPG, PNG ou WEBP, 5 Mo max.)
                    <input type="file" name="property_main_image" accept="image/jpeg,image/png,image/webp">
                </label>
                <label>Texte alternatif de l’image principale
                    <input name="image_alt" placeholder="Cabane en bois avec terrasse dans un environnement naturel">
                </label>
            </div>
            <div class="upload-block">
                <h2>Autres images</h2>
                <p class="form-help">Ajoutez des photos complémentaires pour présenter la terrasse, l’intérieur, la vue ou les équipements.</p>
                <label>Autres images (JPG, PNG ou WEBP, 5 Mo max. par image)
                    <input type="file" name="property_images[]" accept="image/jpeg,image/png,image/webp" multiple>
                </label>
            </div>
            <p class="notice">Si aucune image principale n’est importée, un visuel de remplacement accessible est utilisé.</p>
        </fieldset>

        <button class="button" type="submit"><?= $requiresValidation ? 'Soumettre les modifications' : 'Enregistrer' ?></button>
    </form>
    <?php if ($isEdit): ?>
        <?php
        $mainImages = array_values(array_filter($images ?? [], static fn (array $image): bool => (int) $image['is_main'] === 1));
        $secondaryImages = array_values(array_filter($images ?? [], static fn (array $image): bool => (int) $image['is_main'] !== 1));
        ?>
        <aside class="panel gallery-manager" aria-labelledby="gallery-title">
            <h2 id="gallery-title">Galerie du logement</h2>
            <p class="notice">Chaque image doit avoir un texte alternatif clair pour l’accessibilité et le SEO.</p>
            <?php if ($requiresValidation): ?>
                <p class="notice">Pour un logement publié ou en pause, remplacez l’image principale ou ajoutez des images via le formulaire : ces changements seront soumis à validation admin.</p>
            <?php endif; ?>
            <h3>Image principale actuelle</h3>
            <div class="owner-gallery-grid">
                <?php foreach ($mainImages as $image): ?>
                    <article role="article" class="owner-image-card">
                        <img src="<?= image_url($image['image_path']) ?>" alt="<?= e($image['alt_text']) ?>" loading="lazy">
                        <span class="badge <?= (int) $image['is_main'] === 1 ? 'published' : '' ?>"><?= (int) $image['is_main'] === 1 ? 'Image principale' : 'Image secondaire' ?></span>
                        <?php if (!$requiresValidation): ?>
                            <form role="form" method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/alt') ?>">
                                <?= csrf_field() ?>
                                <label>Texte alternatif<input name="alt_text" value="<?= e($image['alt_text']) ?>" required></label>
                                <button class="button compact" type="submit">Mettre à jour</button>
                            </form>
                            <div class="actions-row">
                                <?php if ((int) $image['is_main'] !== 1): ?>
                                    <form role="form" method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/principale') ?>"><?= csrf_field() ?><button class="button ghost compact" type="submit">Définir principale</button></form>
                                <?php endif; ?>
                                <form role="form" method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/supprimer') ?>" data-confirm="Supprimer cette image du logement ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </div>
            <h3>Autres images</h3>
            <div class="owner-gallery-grid">
                <?php foreach ($secondaryImages as $image): ?>
                    <article role="article" class="owner-image-card">
                        <img src="<?= image_url($image['image_path']) ?>" alt="<?= e($image['alt_text']) ?>" loading="lazy">
                        <span class="badge muted">Image secondaire</span>
                        <?php if (!$requiresValidation): ?>
                            <form role="form" method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/alt') ?>">
                                <?= csrf_field() ?>
                                <label>Texte alternatif<input name="alt_text" value="<?= e($image['alt_text']) ?>" required></label>
                                <button class="button compact" type="submit">Mettre à jour</button>
                            </form>
                            <div class="actions-row">
                                <form role="form" method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/principale') ?>"><?= csrf_field() ?><button class="button ghost compact" type="submit">Définir principale</button></form>
                                <form role="form" method="post" action="<?= url('/proprietaire/logements/' . $property['id'] . '/images/' . $image['id'] . '/supprimer') ?>" data-confirm="Supprimer cette image du logement ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form>
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
                <?php if (!$secondaryImages): ?><p class="empty-state">Aucune image secondaire pour le moment.</p><?php endif; ?>
            </div>
        </aside>
    <?php endif; ?>
</section>
