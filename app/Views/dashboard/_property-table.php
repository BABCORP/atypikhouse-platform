<div class="table-wrap"><table>
    <caption>Logements et statuts de publication</caption>
    <thead><tr><th>Titre</th><th>Ville</th><th>Type</th><th>Prix</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach (($properties ?? []) as $property): ?>
        <tr>
            <td><?= e($property['title']) ?></td>
            <td><?= e($property['city']) ?></td>
            <td><?= property_type_label($property['type']) ?></td>
            <td><?= money($property['price_per_night']) ?></td>
            <td><span class="badge <?= e($property['status']) ?>"><?= status_label($property['status']) ?></span></td>
            <td class="table-actions">
                <?php if (($property['owner_id'] ?? null) && \App\Core\Auth::user()['role'] === 'owner' && in_array($property['status'], ['draft', 'rejected', 'pending'], true)): ?>
                    <a class="button compact ghost" href="<?= url('/proprietaire/logements/' . $property['id'] . '/modifier') ?>">Modifier</a>
                    <?php if (in_array($property['status'], ['draft', 'rejected'], true)): ?><form method="post" class="inline-form" action="<?= url('/proprietaire/logements/' . $property['id'] . '/soumettre') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Soumettre</button></form><?php endif; ?>
                    <?php if (in_array($property['status'], ['draft', 'rejected'], true)): ?><form method="post" class="inline-form" action="<?= url('/proprietaire/logements/' . $property['id'] . '/supprimer') ?>" data-confirm="Supprimer ce brouillon de logement ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form><?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($properties)): ?>
        <tr><td colspan="6" class="empty-state">Aucun logement à afficher.</td></tr>
    <?php endif; ?>
    </tbody>
</table></div>
