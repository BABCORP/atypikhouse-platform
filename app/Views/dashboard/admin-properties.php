<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Back-office</p><h1>Gestion des logements</h1></div></div>
    <div class="table-wrap"><table>
        <caption>Validation et publication des logements</caption>
        <thead><tr><th>Titre</th><th>Propriétaire</th><th>Ville</th><th>Statut</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($properties as $property): ?>
            <tr>
                <td><?= e($property['title']) ?></td>
                <td><?= e($property['owner_email']) ?></td>
                <td><?= e($property['city']) ?></td>
                <td><span class="badge <?= e($property['status']) ?>"><?= status_label($property['status']) ?></span></td>
                <td><form class="inline-form" method="post" action="<?= url('/admin/logements/' . $property['id'] . '/statut') ?>" data-confirm="Confirmer le changement de statut de ce logement ?"><?= csrf_field() ?><select name="status"><?php foreach (['published', 'rejected', 'archived', 'pending'] as $status): ?><option value="<?= $status ?>" <?= $property['status'] === $status ? 'selected' : '' ?>><?= status_label($status) ?></option><?php endforeach; ?></select><button class="button compact" type="submit">Appliquer</button></form></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</section>
