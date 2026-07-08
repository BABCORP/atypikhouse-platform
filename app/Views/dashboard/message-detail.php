<?php require __DIR__ . '/_nav.php'; ?>
<section class="section narrow">
    <article class="panel">
        <p class="eyebrow">Message reçu</p>
        <h1><?= e($message['subject']) ?></h1>
        <dl class="detail-list">
            <dt>Nom</dt><dd><?= e($message['name']) ?></dd>
            <dt>Email</dt><dd><?= e($message['email']) ?></dd>
            <dt>Statut</dt><dd><span class="badge <?= e($message['status']) ?>"><?= status_label($message['status']) ?></span></dd>
            <dt>Date</dt><dd><?= e($message['created_at']) ?></dd>
        </dl>
        <p><?= nl2br(e($message['message'])) ?></p>
        <div class="actions-row">
            <form method="post" action="<?= url('/admin/messages/' . $message['id'] . '/lu') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Marquer lu</button></form>
            <form method="post" action="<?= url('/admin/messages/' . $message['id'] . '/traite') ?>"><?= csrf_field() ?><button class="button compact ghost" type="submit">Marquer traité</button></form>
            <form method="post" action="<?= url('/admin/messages/' . $message['id'] . '/archiver') ?>" data-confirm="Archiver ce message ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Archiver</button></form>
            <a class="button ghost compact" href="<?= url('/admin/messages') ?>">Retour</a>
        </div>
    </article>
</section>
