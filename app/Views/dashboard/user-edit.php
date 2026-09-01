<?php require __DIR__ . '/_nav.php'; ?>
<section class="section narrow">
    <form role="form" class="panel" method="post">
        <?= csrf_field() ?>
        <p class="eyebrow">Administration</p>
        <h1>Modifier <?= e($editUser['first_name'] . ' ' . $editUser['last_name']) ?></h1>
        <label>Prénom<input required name="first_name" placeholder="Ex. Bianca" value="<?= e($editUser['first_name']) ?>"></label>
        <label>Nom<input required name="last_name" placeholder="Ex. Bumb" value="<?= e($editUser['last_name']) ?>"></label>
        <label>Email<input required type="email" name="email" placeholder="vous@exemple.fr" value="<?= e($editUser['email']) ?>"></label>
        <label>Téléphone<input name="phone" placeholder="Ex. 06 12 34 56 78" value="<?= e($editUser['phone']) ?>"></label>
        <label>Rôle
            <select name="role">
                <?php foreach (['tenant', 'owner', 'admin'] as $role): ?>
                    <option value="<?= $role ?>" <?= $editUser['role'] === $role ? 'selected' : '' ?>><?= role_label($role) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Statut
            <select name="status">
                <?php foreach (['active', 'pending', 'rejected', 'suspended'] as $status): ?>
                    <option value="<?= $status ?>" <?= $editUser['status'] === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <p class="notice">Le dernier administrateur actif ne peut pas être suspendu, rétrogradé ou désactivé.</p>
        <div class="actions-row">
            <button class="button" type="submit">Enregistrer</button>
            <a class="button ghost" href="<?= url('/admin/utilisateurs') ?>">Annuler</a>
        </div>
    </form>
</section>
