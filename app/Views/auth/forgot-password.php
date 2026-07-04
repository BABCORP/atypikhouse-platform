<section class="section narrow">
    <form class="panel" method="post" data-track="form_submit">
        <?= csrf_field() ?>
        <p class="eyebrow">Accès compte</p>
        <h1>Mot de passe oublié</h1>
        <p>Indiquez votre email. Pour cette démonstration locale, aucun email réel n’est envoyé : le lien de réinitialisation s’affiche après validation.</p>
        <label>Email
            <input required type="email" name="email" autocomplete="email">
        </label>
        <button class="button full" type="submit">Générer le lien de démonstration</button>
        <p><a href="<?= url('/connexion') ?>">Retour à la connexion</a></p>
    </form>
    <?php if ($demoLink = flash('demo_link')): ?>
        <div class="panel demo-reset" role="status">
            <strong>Lien local de démonstration</strong>
            <p><?= e($demoLink) ?></p>
        </div>
    <?php endif; ?>
</section>
