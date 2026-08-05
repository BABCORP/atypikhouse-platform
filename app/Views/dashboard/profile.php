<?php require __DIR__ . '/_nav.php'; ?>
<section class="section form-layout narrow"><form role="form" class="panel" method="post"><?= csrf_field() ?><h1><?= e($title) ?></h1><label>Prénom<input name="first_name" value="<?= e($user['first_name']) ?>"></label><label>Nom<input name="last_name" value="<?= e($user['last_name']) ?>"></label><label>Téléphone<input name="phone" value="<?= e($user['phone']) ?>"></label><button class="button">Enregistrer</button></form></section>
<?php if ($user['role'] === 'owner'): ?>
<section class="section form-layout narrow">
    <form role="form" class="panel" method="post" action="<?= url('/proprietaire/profil') ?>">
        <?= csrf_field() ?><h2>Informations d’hôte</h2>
        <input type="hidden" name="first_name" value="<?= e($user['first_name']) ?>"><input type="hidden" name="last_name" value="<?= e($user['last_name']) ?>"><input type="hidden" name="phone" value="<?= e($user['phone']) ?>">
        <label>Nom commercial<input name="company_name" value="<?= e($ownerProfile['company_name'] ?? '') ?>"></label>
        <label>Adresse<input name="address" value="<?= e($ownerProfile['address'] ?? '') ?>"></label>
        <label>Ville<input name="city" value="<?= e($ownerProfile['city'] ?? '') ?>"></label>
        <label>Code postal<input name="postal_code" value="<?= e($ownerProfile['postal_code'] ?? '') ?>"></label>
        <label>Pays<input name="country" value="<?= e($ownerProfile['country'] ?? 'France') ?>"></label>
        <label>Présentation<textarea name="description"><?= e($ownerProfile['description'] ?? '') ?></textarea></label>
        <button class="button">Enregistrer le profil hôte</button>
    </form>
</section>
<?php endif; ?>
<?php if ($user['role'] === 'tenant'): ?>
<section class="section form-layout narrow">
    <form role="form" class="panel" method="post" action="<?= url('/locataire/profil/mot-de-passe') ?>">
        <?= csrf_field() ?><h2>Changer mon mot de passe</h2>
        <label>Mot de passe actuel<input required type="password" name="current_password"></label>
        <label>Nouveau mot de passe<input required minlength="8" type="password" name="password"></label>
        <label>Confirmer<input required minlength="8" type="password" name="password_confirmation"></label>
        <button class="button">Mettre à jour</button>
    </form>
</section>
<?php endif; ?>
