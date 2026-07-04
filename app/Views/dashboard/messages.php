<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Relation client</p><h1>Messages de contact</h1></div></div>
    <div class="dashboard-grid two">
        <?php foreach ($messages as $message): ?>
            <article class="panel">
                <h2><?= e($message['subject']) ?></h2>
                <p><?= e($message['name']) ?> · <?= e($message['email']) ?> · <span class="badge <?= e($message['status']) ?>"><?= status_label($message['status']) ?></span>
                    <?php if (str_starts_with($message['subject'], 'Demande RGPD')): ?><span class="badge pending">Demande RGPD</span><?php endif; ?>
                    <?php if ($message['subject'] === 'Newsletter'): ?><span class="badge muted">Newsletter démo</span><?php endif; ?>
                </p>
                <p><?= e($message['message']) ?></p>
                <form class="inline-form" method="post" action="<?= url('/admin/messages/' . $message['id'] . '/statut') ?>"><?= csrf_field() ?><button class="button compact" name="status" value="read" type="submit">Marquer lu</button><button class="button ghost compact" name="status" value="archived" type="submit">Archiver</button></form>
            </article>
        <?php endforeach; ?>
    </div>
</section>
