<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Back-office</p>
            <h1>Utilisateurs</h1>
        </div>
    </div>
    <form class="filters panel" method="get">
        <label>Rôle
            <select name="role">
                <option value="">Tous</option>
                <?php foreach (['tenant', 'owner', 'admin'] as $role): ?>
                    <option value="<?= $role ?>" <?= input('role') === $role ? 'selected' : '' ?>><?= role_label($role) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Statut
            <select name="status">
                <option value="">Tous</option>
                <?php foreach (['active', 'pending', 'suspended'] as $status): ?>
                    <option value="<?= $status ?>" <?= input('status') === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button compact" type="submit">Filtrer</button>
    </form>
    <div class="table-wrap"><table>
        <caption>Comptes utilisateurs et statuts</caption>
        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= e($u['first_name'] . ' ' . $u['last_name']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= role_label($u['role']) ?></td>
                <td><span class="badge <?= e($u['status']) ?>"><?= status_label($u['status']) ?></span></td>
                <td class="table-actions">
                    <a class="button compact ghost" href="<?= url('/admin/utilisateurs/' . $u['id'] . '/modifier') ?>">Modifier</a>
                    <form method="post" action="<?= url('/admin/utilisateurs/' . $u['id'] . '/statut') ?>">
                        <?= csrf_field() ?>
                        <select name="status" aria-label="Changer le statut de <?= e($u['email']) ?>">
                            <?php foreach (['active', 'pending', 'suspended'] as $status): ?>
                                <option value="<?= $status ?>" <?= $u['status'] === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="button compact" type="submit">Appliquer</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table></div>
    <?php require __DIR__ . '/_pagination.php'; ?>
</section>
