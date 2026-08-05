<section class="page-hero compact"><p class="eyebrow">AtypikHouse</p><h1>Nous contacter</h1><p>Une question sur un séjour ou l’espace hôte ? Notre équipe vous répond.</p></section>
<section class="section contact-layout">
    <aside class="contact-details"><h2>Nos coordonnées</h2><p><strong>Adresse</strong><br>Pierrefonds, Oise<br>France</p><p><strong>Email</strong><br>contact@atypikhouse.fr</p><p><strong>Téléphone</strong><br>03 44 88 21 10</p><p><strong>Horaires</strong><br>Lundi - Vendredi<br>9h00 - 18h00</p><p class="notice"><?= e(config('academic_disclaimer')) ?></p></aside>
    <form role="form" method="post" class="panel contact-form" data-track="form_submit">
        <h2>Envoyez-nous un message</h2>
        <?= csrf_field() ?>
        <div class="form-grid">
        <label>Nom complet<input required name="name" value="<?= old('name') ?>"></label>
        <label>Adresse email<input required type="email" name="email" value="<?= old('email') ?>"></label>
        </div>
        <label>Sujet<input required name="subject" value="<?= old('subject') ?>"></label>
        <label>Votre message<textarea required name="message"><?= old('message') ?></textarea></label>
        <label class="consent"><input required type="checkbox" name="privacy_consent" value="1"> J’accepte que mes données soient utilisées pour être recontacté dans le cadre de cette démonstration académique.</label>
        <button class="button" type="submit">Envoyer le message</button>
    </form>
</section>
