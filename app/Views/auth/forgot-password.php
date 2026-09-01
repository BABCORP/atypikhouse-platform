<section class="section narrow">
    <form role="form" class="panel" method="post" data-track="form_submit">
        <?= csrf_field() ?>
        <p class="eyebrow">Accès compte</p>
        <h1>Mot de passe oublié</h1>
        <p>Indiquez votre email. Le lien de réinitialisation est disponible dans la page et peut aussi être envoyé par email si le SMTP Brevo est activé.</p>
        <label>Email
            <input required type="email" name="email" autocomplete="email" placeholder="vous@exemple.fr">
        </label>
        <button class="button full" type="submit">Générer le lien de réinitialisation</button>
        <p><a href="<?= url('/connexion') ?>">Retour à la connexion</a></p>
    </form>
    <?php if ($demoLink = flash('demo_link')): ?>
        <div class="panel demo-reset" role="status">
            <strong>Lien de réinitialisation</strong>
            <p><?= e($demoLink) ?></p>
        </div>
    <?php endif; ?>
</section>
