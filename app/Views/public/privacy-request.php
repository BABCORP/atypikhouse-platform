<section class="page-hero compact">
    <p class="eyebrow">Données personnelles</p>
    <h1>Demande relative aux données personnelles</h1>
    <p>Ce formulaire permet de déposer une demande d’accès, de rectification, de suppression ou d’opposition dans le cadre du projet étudiant AtypikHouse.</p>
</section>
<section class="section contact-layout">
    <aside class="contact-details">
        <h2>Cadre académique</h2>
        <p><?= e(config('academic_disclaimer')) ?></p>
        <p>La demande est stockée localement dans les messages administrateur. Aucun traitement juridique réel ni suppression automatique de compte n’est exécuté.</p>
        <a class="text-link" href="<?= url('/politique-confidentialite') ?>">Lire la politique de confidentialité</a>
    </aside>
    <form role="form" class="panel contact-form" method="post" data-track="form_submit">
        <?= csrf_field() ?>
        <label>Nom complet<input required name="name" value="<?= old('name') ?>"></label>
        <label>Email<input required type="email" name="email" value="<?= old('email') ?>"></label>
        <label>Type de demande
            <select required name="request_type">
                <option value="access">Accès à mes données</option>
                <option value="rectification">Rectification</option>
                <option value="deletion">Suppression</option>
                <option value="opposition">Opposition</option>
            </select>
        </label>
        <label>Message<textarea required name="message"><?= old('message') ?></textarea></label>
        <label class="consent"><input required type="checkbox" name="privacy_consent" value="1"> J’accepte que cette demande soit stockée dans le cadre de la démonstration académique.</label>
        <button class="button full" type="submit">Envoyer la demande</button>
    </form>
</section>
