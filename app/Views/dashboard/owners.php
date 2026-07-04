<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Back-office</p><h1>Profils propriétaires</h1></div></div>
    <div class="table-wrap"><table>
        <caption>Validation des profils hôtes</caption>
        <thead><tr><th>Propriétaire</th><th>Société</th><th>Ville</th><th>Statut</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($owners as $owner): ?>
            <tr>
                <td><?= e($owner['email']) ?></td>
                <td><?= e($owner['company_name']) ?></td>
                <td><?= e($owner['city']) ?></td>
                <td><span class="badge <?= e($owner['verification_status']) ?>"><?= status_label($owner['verification_status']) ?></span></td>
                <td><form class="inline-form" method="post" action="<?= url('/admin/proprietaires/' . $owner['id'] . '/statut') ?>"><?= csrf_field() ?><select name="status"><?php foreach (['approved', 'pending', 'rejected'] as $status): ?><option value="<?= $status ?>" <?= $owner['verification_status'] === $status ? 'selected' : '' ?>><?= status_label($status) ?></option><?php endforeach; ?></select><button class="button compact" type="submit">Modérer</button></form></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</section>
