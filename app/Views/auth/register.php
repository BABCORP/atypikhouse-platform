<section class="section form-layout narrow">
    <form role="form" class="panel" method="post" data-track="owner_signup">
        <h1>Créer un compte</h1>
        <?= csrf_field() ?>
        <label>Type de compte<select name="role" id="role-select"><option value="tenant" <?= $role !== 'owner' ? 'selected' : '' ?>>Locataire</option><option value="owner" <?= $role === 'owner' ? 'selected' : '' ?>>Propriétaire</option></select></label>
        <label>Prénom<input required name="first_name" value="<?= old('first_name') ?>"></label>
        <label>Nom<input required name="last_name" value="<?= old('last_name') ?>"></label>
        <label>Email<input required type="email" name="email" autocomplete="email" value="<?= old('email') ?>"></label>
        <label>Téléphone<input name="phone" value="<?= old('phone') ?>"></label>
        <label>Mot de passe<input required type="password" name="password" minlength="8" autocomplete="new-password"></label>
        <fieldset class="owner-fields"><legend>Profil propriétaire</legend>
            <label>Nom commercial<input name="company_name" value="<?= old('company_name') ?>"></label>
            <label>Adresse<input name="address" value="<?= old('address') ?>"></label>
            <label>Ville<input name="city" value="<?= old('city') ?>"></label>
            <label>Code postal<input name="postal_code" value="<?= old('postal_code') ?>"></label>
            <label>Description<textarea name="description"><?= old('description') ?></textarea></label>
        </fieldset>
        <button class="button full" type="submit">Créer un compte</button>
    </form>
</section>
