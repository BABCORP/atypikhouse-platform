<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Back-office</p>
            <h1>Utilisateurs</h1>
        </div>
    </div>
    <form role="form" class="filters panel" method="get">
        <label>Recherche
            <input name="search" value="<?= e(input('search', '')) ?>" placeholder="Nom, prénom ou email">
        </label>
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
                <?php foreach (['active', 'pending', 'rejected', 'suspended'] as $status): ?>
                    <option value="<?= $status ?>" <?= input('status') === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button compact" type="submit">Filtrer</button>
    </form>
    <div class="table-wrap"><table>
        <caption>Comptes utilisateurs et statuts</caption>
        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Création</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= e($u['first_name'] . ' ' . $u['last_name']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= role_label($u['role']) ?></td>
                <td><span class="badge <?= e($u['status']) ?>"><?= status_label($u['status']) ?></span></td>
                <td><?= e($u['created_at']) ?></td>
                <td class="table-actions">
                    <a class="button compact ghost" href="<?= url('/admin/utilisateurs/' . $u['id']) ?>">Voir</a>
                    <a class="button compact ghost" href="<?= url('/admin/utilisateurs/' . $u['id'] . '/modifier') ?>">Modifier</a>
                    <?php if ($u['status'] !== 'active'): ?>
                        <form role="form" method="post" action="<?= url('/admin/utilisateurs/' . $u['id'] . '/approuver') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Approuver</button></form>
                    <?php endif; ?>
                    <?php if ($u['status'] !== 'rejected'): ?>
                        <form role="form" method="post" action="<?= url('/admin/utilisateurs/' . $u['id'] . '/refuser') ?>" data-confirm="Refuser ce compte utilisateur ?"><?= csrf_field() ?><button class="button ghost compact" type="submit">Refuser</button></form>
                    <?php endif; ?>
                    <?php if ($u['status'] === 'suspended'): ?>
                        <form role="form" method="post" action="<?= url('/admin/utilisateurs/' . $u['id'] . '/reactiver') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Réactiver</button></form>
                    <?php else: ?>
                        <form role="form" method="post" action="<?= url('/admin/utilisateurs/' . $u['id'] . '/suspendre') ?>" data-confirm="Suspendre ce compte utilisateur ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Suspendre</button></form>
                    <?php endif; ?>
                    <form role="form" method="post" action="<?= url('/admin/utilisateurs/' . $u['id'] . '/statut') ?>">
                        <?= csrf_field() ?>
                        <select name="status" aria-label="Changer le statut de <?= e($u['email']) ?>">
                            <?php foreach (['active', 'pending', 'rejected', 'suspended'] as $status): ?>
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
