<?php require __DIR__ . '/_nav.php'; ?>
<?php
$proposed = $request['proposed'] ?? [];
$fields = [
    'title' => 'Titre',
    'type' => 'Type',
    'short_description' => 'Description courte',
    'long_description' => 'Description détaillée',
    'address' => 'Adresse',
    'city' => 'Ville',
    'postal_code' => 'Code postal',
    'region' => 'Région',
    'country' => 'Pays',
    'capacity' => 'Capacité',
    'bedrooms' => 'Chambres',
    'beds' => 'Lits',
    'bathrooms' => 'Salles de bain',
    'price_per_night' => 'Prix par nuit',
    'cleaning_fee' => 'Frais de ménage',
    'eco_score' => 'Eco-score',
];
?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Validation modification</p>
            <h1><?= e($request['title']) ?></h1>
            <p><?= e($request['owner_email']) ?> · <span class="badge <?= e($request['status']) ?>"><?= status_label($request['status']) ?></span></p>
        </div>
        <a class="button ghost" href="<?= url('/admin/logements/modifications') ?>">Retour</a>
    </div>

    <article class="panel">
        <h2>Comparaison des informations</h2>
        <div class="table-wrap"><table>
            <caption>Anciennes et nouvelles valeurs proposées</caption>
            <thead><tr><th>Champ</th><th>Valeur publiée</th><th>Valeur proposée</th></tr></thead>
            <tbody>
            <?php foreach ($fields as $key => $label): ?>
                <?php
                $currentValue = $key === 'type' ? property_type_label($request[$key] ?? '') : (string) ($request[$key] ?? '');
                $proposedValue = $key === 'type' ? property_type_label($proposed[$key] ?? '') : (string) ($proposed[$key] ?? '');
                $changed = $currentValue !== $proposedValue;
                ?>
                <tr class="<?= $changed ? 'is-changed' : '' ?>">
                    <td><?= e($label) ?></td>
                    <td><?= nl2br(e($currentValue)) ?></td>
                    <td><?= nl2br(e($proposedValue)) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table></div>
    </article>

    <div class="dashboard-grid two">
        <article class="panel">
            <h2>Équipements actuels</h2>
            <div class="pill-list amenity-list"><?php foreach (($currentAmenities ?? []) as $amenity): ?><span><?= e($amenity) ?></span><?php endforeach; ?></div>
        </article>
        <article class="panel">
            <h2>Équipements proposés</h2>
            <div class="pill-list amenity-list"><?php foreach (($proposed['amenities'] ?? []) as $amenity): ?><span><?= e($amenity) ?></span><?php endforeach; ?></div>
        </article>
    </div>

    <div class="dashboard-grid two">
        <article class="panel">
            <h2>Images actuelles</h2>
            <?php foreach (($currentImages ?? []) as $image): ?>
                <figure>
                    <img class="property-admin-image" src="<?= image_url($image['image_path']) ?>" alt="<?= e($image['alt_text']) ?>">
                    <figcaption><?= (int) $image['is_main'] === 1 ? 'Image principale · ' : 'Image secondaire · ' ?><?= e($image['alt_text']) ?></figcaption>
                </figure>
            <?php endforeach; ?>
        </article>
        <article class="panel">
            <h2>Images proposées</h2>
            <?php if (!empty($proposed['main_image']['path'])): ?>
                <figure>
                    <img class="property-admin-image" src="<?= image_url($proposed['main_image']['path']) ?>" alt="<?= e($proposed['main_image']['alt_text'] ?? '') ?>">
                    <figcaption>Nouvelle image principale · <?= e($proposed['main_image']['alt_text'] ?? '') ?></figcaption>
                </figure>
            <?php endif; ?>
            <?php foreach (($proposed['secondary_images'] ?? []) as $image): ?>
                <?php if (!empty($image['path'])): ?>
                    <figure>
                        <img class="property-admin-image" src="<?= image_url($image['path']) ?>" alt="<?= e($image['alt_text'] ?? '') ?>">
                        <figcaption>Nouvelle image secondaire · <?= e($image['alt_text'] ?? '') ?></figcaption>
                    </figure>
                <?php endif; ?>
            <?php endforeach; ?>
            <?php if (empty($proposed['main_image']['path']) && empty($proposed['secondary_images'])): ?><p>Aucune nouvelle image proposée.</p><?php endif; ?>
        </article>
    </div>

    <article class="panel">
        <h2>Décision administrateur</h2>
        <div class="actions-row">
            <form method="post" action="<?= url('/admin/logements/modifications/' . $request['id'] . '/approuver') ?>">
                <?= csrf_field() ?>
                <button class="button" type="submit">Approuver et publier</button>
            </form>
            <form method="post" action="<?= url('/admin/logements/modifications/' . $request['id'] . '/refuser') ?>" data-confirm="Refuser cette demande de modification ?">
                <?= csrf_field() ?>
                <label>Motif de refus
                    <textarea name="rejection_reason" maxlength="500" placeholder="Ex. les photos ne sont pas assez cohérentes avec le logement."></textarea>
                </label>
                <button class="button danger" type="submit">Refuser</button>
            </form>
        </div>
    </article>
</section>
