<section class="section narrow">
    <form role="form" class="panel" method="post" data-track="form_submit">
        <?= csrf_field() ?>
        <p class="eyebrow">Sécurité du compte</p>
        <h1>Créer un nouveau mot de passe</h1>
        <label>Nouveau mot de passe
            <input required type="password" name="password" minlength="8" autocomplete="new-password" placeholder="Minimum 8 caractères">
        </label>
        <label>Confirmation
            <input required type="password" name="password_confirmation" minlength="8" autocomplete="new-password" placeholder="Répétez le nouveau mot de passe">
        </label>
        <button class="button full" type="submit">Mettre à jour le mot de passe</button>
    </form>
</section>
