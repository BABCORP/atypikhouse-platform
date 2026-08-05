<section class="section form-layout narrow">
    <form role="form" class="panel" method="post">
        <h1>Se connecter</h1>
        <?= csrf_field() ?>
        <label>Email<input required type="email" name="email" autocomplete="email" placeholder="vous@exemple.fr" value="<?= old('email') ?>"></label>
        <label>Mot de passe<input required type="password" name="password" autocomplete="current-password" placeholder="Votre mot de passe"></label>
        <button class="button full" type="submit" data-track="login_success">Se connecter</button>
        <p><a href="<?= url('/mot-de-passe-oublie') ?>">Mot de passe oublié ?</a></p>
        <p>Pas encore de compte ? <a href="<?= url('/inscription') ?>">Créer un compte</a></p>
    </form>
</section>
