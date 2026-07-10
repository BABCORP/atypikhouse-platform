<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Back-office</p><h1>Gestion des logements</h1></div></div>
    <form class="filters panel" method="get">
        <label>Recherche<input name="search" value="<?= e(input('search', '')) ?>" placeholder="Titre du logement"></label>
        <label>Propriétaire<input name="owner" value="<?= e(input('owner', '')) ?>" placeholder="Email ou nom"></label>
        <label>Localisation<input name="location" value="<?= e(input('location', '')) ?>" placeholder="Ville ou région"></label>
        <label>Statut
            <select name="status">
                <option value="">Tous</option>
                <?php foreach (['draft', 'pending', 'published', 'paused', 'rejected', 'archived', 'deleted'] as $status): ?>
                    <option value="<?= $status ?>" <?= input('status') === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Type
            <select name="type">
                <option value="">Tous</option>
                <?php foreach (['treehouse', 'yurt', 'floating_cabin', 'tiny_house', 'dome', 'other'] as $type): ?>
                    <option value="<?= $type ?>" <?= input('type') === $type ? 'selected' : '' ?>><?= property_type_label($type) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button compact" type="submit">Filtrer</button>
    </form>
    <div class="table-wrap"><table>
        <caption>Validation et publication des logements</caption>
        <thead><tr><th>Image</th><th>Titre</th><th>Propriétaire</th><th>Ville</th><th>Type</th><th>Prix</th><th>Statut</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($properties as $property): ?>
            <tr>
                <td><img class="table-thumb" src="<?= image_url($property['main_image'] ?? null) ?>" alt=""></td>
                <td><a href="<?= url('/admin/logements/' . $property['id']) ?>"><?= e($property['title']) ?></a></td>
                <td><?= e($property['owner_email']) ?></td>
                <td><?= e($property['city']) ?></td>
                <td><?= property_type_label($property['type']) ?></td>
                <td><?= money($property['price_per_night']) ?></td>
                <td>
                    <span class="badge <?= e($property['status']) ?>"><?= status_label($property['status']) ?></span>
                    <?php if (($property['pending_change_count'] ?? 0) > 0): ?>
                        <a class="badge pending" href="<?= url('/admin/logements/modifications') ?>">Modification en attente</a>
                    <?php endif; ?>
                </td>
                <td class="table-actions">
                    <a class="button compact ghost" href="<?= url('/admin/logements/' . $property['id']) ?>">Voir</a>
                    <?php if ($property['status'] !== 'deleted'): ?>
                        <?php if ($property['status'] !== 'paused'): ?><a class="button compact ghost" href="<?= url('/admin/logements/' . $property['id'] . '/modifier') ?>">Modifier</a><?php endif; ?>
                        <?php if (in_array($property['status'], ['pending', 'rejected', 'draft'], true)): ?>
                            <form class="inline-form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/approuver') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Publier</button></form>
                        <?php endif; ?>
                        <?php if ($property['status'] === 'pending'): ?>
                            <form class="inline-form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/refuser') ?>" data-confirm="Refuser ce logement ?"><?= csrf_field() ?><button class="button ghost compact" type="submit">Refuser</button></form>
                        <?php endif; ?>
                        <?php if ($property['status'] === 'published'): ?>
                            <form class="inline-form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/mettre-en-pause') ?>" data-confirm="Mettre ce logement en pause ? Les réservations existantes seront conservées."><?= csrf_field() ?><button class="button ghost compact" type="submit">Mettre en pause</button></form>
                        <?php endif; ?>
                        <?php if ($property['status'] === 'paused'): ?>
                            <form class="inline-form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/reactiver') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Réactiver</button></form>
                        <?php endif; ?>
                        <form class="inline-form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/supprimer') ?>" data-confirm="Confirmer la suppression de ce logement ? Il ne sera plus visible publiquement, mais son historique sera conservé."><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($properties)): ?><tr><td colspan="8" class="empty-state">Aucun logement ne correspond aux critères.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
    <?php require __DIR__ . '/_pagination.php'; ?>
</section>
