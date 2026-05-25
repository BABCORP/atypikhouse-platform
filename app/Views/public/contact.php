<section class="page-hero compact"><h1>Contact</h1><p>Une question sur le projet fictif AtypikHouse ? Écrivez-nous.</p></section>
<section class="section form-layout">
    <form method="post" class="panel" data-track="form_submit">
        <?= csrf_field() ?>
        <label>Nom<input required name="name" value="<?= old('name') ?>"></label>
        <label>Email<input required type="email" name="email" value="<?= old('email') ?>"></label>
        <label>Sujet<input required name="subject" value="<?= old('subject') ?>"></label>
        <label>Message<textarea required name="message"><?= old('message') ?></textarea></label>
        <button class="button" type="submit">Envoyer</button>
    </form>
    <aside class="panel"><h2>AtypikHouse SARL fictive</h2><p>Pierrefonds, Oise, France</p><p><?= e(config('academic_disclaimer')) ?></p></aside>
</section>
