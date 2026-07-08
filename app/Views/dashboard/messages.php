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
                <div class="actions-row">
                    <a class="button ghost compact" href="<?= url('/admin/messages/' . $message['id']) ?>">Voir</a>
                    <form class="inline-form" method="post" action="<?= url('/admin/messages/' . $message['id'] . '/lu') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Marquer lu</button></form>
                    <form class="inline-form" method="post" action="<?= url('/admin/messages/' . $message['id'] . '/traite') ?>"><?= csrf_field() ?><button class="button ghost compact" type="submit">Traité</button></form>
                    <form class="inline-form" method="post" action="<?= url('/admin/messages/' . $message['id'] . '/archiver') ?>" data-confirm="Archiver ce message ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Archiver</button></form>
                </div>
            </article>
        <?php endforeach; ?>
        <?php if (empty($messages)): ?><p class="empty-state">Aucun message ne correspond aux critères.</p><?php endif; ?>
    </div>
    <?php require __DIR__ . '/_pagination.php'; ?>
</section>
