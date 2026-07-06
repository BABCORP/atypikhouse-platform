<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading"><div><p class="eyebrow">Relation client</p><h1>Messages de contact</h1></div></div>
    <form class="filters panel" method="get">
        <label>Type de message
            <select name="type">
                <?php foreach (['all' => 'Tous', 'contact' => 'Contact', 'newsletter' => 'Newsletter', 'rgpd' => 'Demandes RGPD'] as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= ($type ?? 'all') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <button class="button compact" type="submit">Filtrer</button>
    </form>
    <div class="dashboard-grid two">
        <?php foreach ($messages as $message): ?>
            <article class="panel">
                <h2><?= e($message['subject']) ?></h2>
                <p><?= e($message['name']) ?> · <?= e($message['email']) ?> · <span class="badge <?= e($message['status']) ?>"><?= status_label($message['status']) ?></span>
                    <?php if (str_starts_with($message['subject'], 'Demande RGPD') && $message['status'] === 'new'): ?><span class="badge pending">À traiter</span><?php endif; ?>
                    <?php if (str_starts_with($message['subject'], 'Demande RGPD')): ?><span class="badge pending">Demande RGPD</span><?php endif; ?>
                    <?php if ($message['subject'] === 'Newsletter'): ?><span class="badge muted">Newsletter démo</span><?php endif; ?>
                </p>
                <p><?= e($message['message']) ?></p>
                <form class="inline-form" method="post" action="<?= url('/admin/messages/' . $message['id'] . '/statut') ?>" data-confirm="Confirmer la mise à jour du statut de ce message ?"><?= csrf_field() ?><button class="button compact" name="status" value="read" type="submit">Marquer lu</button><button class="button ghost compact" name="status" value="archived" type="submit">Archiver</button></form>
            </article>
        <?php endforeach; ?>
    </div>
    <?php require __DIR__ . '/_pagination.php'; ?>
</section>
