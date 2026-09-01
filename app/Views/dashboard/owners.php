<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Back-office</p><h1>Profils propriétaires</h1></div></div>
    <form role="form" class="filters panel" method="get">
        <label>Recherche<input name="search" value="<?= e(input('search', '')) ?>" placeholder="Email, nom ou société"></label>
        <label>Validation
            <select name="status">
                <option value="">Tous</option>
                <?php foreach (['pending', 'approved', 'rejected'] as $status): ?>
                    <option value="<?= $status ?>" <?= input('status') === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button compact" type="submit">Filtrer</button>
    </form>
    <div class="table-wrap"><table>
        <caption>Validation des profils hôtes</caption>
        <thead><tr><th>Propriétaire</th><th>Société</th><th>Ville</th><th>Compte</th><th>Logements</th><th>Statut</th><th>Action</th></tr></thead>
        <tbody>
        <?php foreach ($owners as $owner): ?>
            <tr>
                <td><a href="<?= url('/admin/utilisateurs/' . $owner['user_id']) ?>"><?= e($owner['email']) ?></a></td>
                <td><?= e($owner['company_name']) ?></td>
                <td><?= e($owner['city']) ?></td>
                <td><span class="badge <?= e($owner['user_status']) ?>"><?= status_label($owner['user_status']) ?></span></td>
                <td><?= (int) $owner['properties_count'] ?> total · <?= (int) $owner['published_properties'] ?> publiés · <?= (int) $owner['pending_properties'] ?> attente</td>
                <td><span class="badge <?= e($owner['verification_status']) ?>"><?= status_label($owner['verification_status']) ?></span></td>
                <td><form role="form" class="inline-form" method="post" action="<?= url('/admin/proprietaires/' . $owner['id'] . '/statut') ?>"><?= csrf_field() ?><select name="status"><?php foreach (['approved', 'pending', 'rejected'] as $status): ?><option value="<?= $status ?>" <?= $owner['verification_status'] === $status ? 'selected' : '' ?>><?= status_label($status) ?></option><?php endforeach; ?></select><button class="button compact" type="submit">Modérer</button></form></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
</section>
