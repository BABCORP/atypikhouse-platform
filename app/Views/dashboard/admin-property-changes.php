<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Back-office</p>
            <h1>Modifications de logements à valider</h1>
        </div>
        <a class="button ghost" href="<?= url('/admin/logements') ?>">Retour aux logements</a>
    </div>
    <div class="table-wrap"><table>
        <caption>Demandes de modification soumises par les propriétaires</caption>
        <thead><tr><th>Logement</th><th>Propriétaire</th><th>Statut</th><th>Soumise le</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach (($requests ?? []) as $request): ?>
            <tr>
                <td><?= e($request['title']) ?><br><small><?= e($request['city'] . ' · ' . $request['region']) ?></small></td>
                <td><?= e(trim(($request['owner_first_name'] ?? '') . ' ' . ($request['owner_last_name'] ?? ''))) ?><br><small><?= e($request['owner_email']) ?></small></td>
                <td><span class="badge <?= e($request['status']) ?>"><?= status_label($request['status']) ?></span></td>
                <td><?= e($request['updated_at']) ?></td>
                <td><a class="button compact" href="<?= url('/admin/logements/modifications/' . $request['id']) ?>">Comparer</a></td>
            </tr>
        <?php endforeach; ?>
        <?php if (empty($requests)): ?><tr><td colspan="5" class="empty-state">Aucune modification de logement en attente.</td></tr><?php endif; ?>
        </tbody>
    </table></div>
</section>
