<section class="section form-layout narrow">
    <form class="panel" method="post">
        <h1>Se connecter</h1>
        <?= csrf_field() ?>
        <label>Email<input required type="email" name="email" value="<?= old('email') ?>"></label>
        <label>Mot de passe<input required type="password" name="password"></label>
        <button class="button full" type="submit" data-track="login_success">Se connecter</button>
        <p><a href="<?= url('/mot-de-passe-oublie') ?>">Mot de passe oublié ?</a></p>
        <p>Pas encore de compte ? <a href="<?= url('/inscription') ?>">Créer un compte</a></p>
    </form>
</section>
