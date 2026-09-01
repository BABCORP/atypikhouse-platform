<?php require __DIR__ . '/_nav.php'; ?>
<?php
$deleteAction = $user['role'] === 'owner' ? '/proprietaire/profil/supprimer' : '/locataire/profil/supprimer';
$passwordAction = $user['role'] === 'owner' ? '/proprietaire/profil/mot-de-passe' : '/locataire/profil/mot-de-passe';
?>

<section class="section form-layout narrow">
    <form role="form" class="panel" method="post">
        <?= csrf_field() ?>
        <h1><?= e($title) ?></h1>
        <p class="muted">Vous pouvez modifier vos informations personnelles depuis cet espace.</p>
        <label>Prénom<input required name="first_name" placeholder="Ex. Bianca" value="<?= e($user['first_name']) ?>"></label>
        <label>Nom<input required name="last_name" placeholder="Ex. Bumb" value="<?= e($user['last_name']) ?>"></label>
        <label>Téléphone<input name="phone" placeholder="Ex. 06 12 34 56 78" value="<?= e($user['phone']) ?>"></label>
        <button class="button" type="submit">Enregistrer</button>
    </form>
</section>

<?php if ($user['role'] === 'owner'): ?>
<section class="section form-layout narrow">
    <form role="form" class="panel" method="post" action="<?= url('/proprietaire/profil') ?>">
        <?= csrf_field() ?>
        <h2>Informations d’hôte</h2>
        <input type="hidden" name="first_name" value="<?= e($user['first_name']) ?>">
        <input type="hidden" name="last_name" value="<?= e($user['last_name']) ?>">
        <input type="hidden" name="phone" value="<?= e($user['phone']) ?>">
        <label>Nom commercial<input name="company_name" placeholder="Ex. Les Cabanes de Pierrefonds" value="<?= e($ownerProfile['company_name'] ?? '') ?>"></label>
        <label>Adresse<input name="address" placeholder="Ex. 12 rue du Château" value="<?= e($ownerProfile['address'] ?? '') ?>"></label>
        <label>Ville<input name="city" placeholder="Ex. Pierrefonds" value="<?= e($ownerProfile['city'] ?? '') ?>"></label>
        <label>Code postal<input name="postal_code" placeholder="Ex. 60350" value="<?= e($ownerProfile['postal_code'] ?? '') ?>"></label>
        <label>Pays<input name="country" placeholder="Ex. France" value="<?= e($ownerProfile['country'] ?? 'France') ?>"></label>
        <label>Présentation<textarea name="description" placeholder="Présentez votre activité, votre lieu et votre démarche d’accueil."><?= e($ownerProfile['description'] ?? '') ?></textarea></label>
        <button class="button" type="submit">Enregistrer le profil hôte</button>
    </form>
</section>
<?php endif; ?>

<section class="section form-layout narrow">
    <form role="form" class="panel" method="post" action="<?= url($passwordAction) ?>">
        <?= csrf_field() ?>
        <h2>Changer mon mot de passe</h2>
        <label>Mot de passe actuel<input required type="password" name="current_password" placeholder="Votre mot de passe actuel"></label>
        <label>Nouveau mot de passe<input required minlength="8" type="password" name="password" placeholder="Minimum 8 caractères"></label>
        <label>Confirmer<input required minlength="8" type="password" name="password_confirmation" placeholder="Répétez le nouveau mot de passe"></label>
        <button class="button" type="submit">Mettre à jour</button>
    </form>
</section>

<section class="section form-layout narrow">
    <form role="form" class="panel danger-zone" method="post" action="<?= url($deleteAction) ?>" data-confirm="Confirmer la suppression de votre compte ? Cette action vous déconnectera et ne pourra pas être annulée depuis votre espace.">
        <?= csrf_field() ?>
        <h2>Supprimer mon compte</h2>
        <p>La suppression désactive votre accès et anonymise vos informations personnelles. Les réservations, avis et journaux nécessaires à l’historique du projet étudiant sont conservés.</p>
        <?php if ($user['role'] === 'owner'): ?>
            <p>Pour un compte propriétaire, les logements associés sont également mis en pause afin de ne plus apparaître publiquement.</p>
        <?php endif; ?>
        <label>Mot de passe actuel<input required type="password" name="current_password" placeholder="Confirmez avec votre mot de passe"></label>
        <button class="button danger" type="submit">Supprimer mon compte</button>
    </form>
</section>
