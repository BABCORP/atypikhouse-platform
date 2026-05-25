<div class="table-wrap"><table>
    <thead><tr><th>Titre</th><th>Ville</th><th>Type</th><th>Prix</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach (($properties ?? []) as $property): ?>
        <tr>
            <td><?= e($property['title']) ?></td><td><?= e($property['city']) ?></td><td><?= e($property['type']) ?></td><td><?= money($property['price_per_night']) ?></td><td><span class="badge"><?= e($property['status']) ?></span></td>
            <td>
                <?php if (($property['owner_id'] ?? null) && \App\Core\Auth::user()['role'] === 'owner' && in_array($property['status'], ['draft', 'rejected', 'pending'], true)): ?>
                    <a href="<?= url('/proprietaire/logements/' . $property['id'] . '/modifier') ?>">Modifier</a>
                    <?php if (in_array($property['status'], ['draft', 'rejected'], true)): ?><form method="post" class="inline-form" action="<?= url('/proprietaire/logements/' . $property['id'] . '/soumettre') ?>"><?= csrf_field() ?><button>Soumettre</button></form><?php endif; ?>
                    <?php if (in_array($property['status'], ['draft', 'rejected'], true)): ?><form method="post" class="inline-form" action="<?= url('/proprietaire/logements/' . $property['id'] . '/supprimer') ?>"><?= csrf_field() ?><button class="danger">Supprimer</button></form><?php endif; ?>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table></div>
