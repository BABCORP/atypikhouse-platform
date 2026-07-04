<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow"><?= ($scope ?? '') === 'admin' ? 'Back-office' : 'Espace propriétaire' ?></p>
            <h1><?= e($title) ?></h1>
        </div>
    </div>
    <?php if (($scope ?? '') === 'owner'): ?>
        <form class="filters panel" method="get">
            <label>Logement
                <select name="property_id">
                    <option value="">Tous les logements</option>
                    <?php foreach (($properties ?? []) as $property): ?>
                        <option value="<?= (int) $property['id'] ?>" <?= (($filters['property_id'] ?? '') == $property['id']) ? 'selected' : '' ?>><?= e($property['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Statut
                <select name="status">
                    <option value="">Tous</option>
                    <?php foreach (['pending_payment', 'confirmed', 'cancelled', 'completed'] as $status): ?>
                        <option value="<?= $status ?>" <?= (($filters['status'] ?? '') === $status) ? 'selected' : '' ?>><?= status_label($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>Début après<input type="date" name="start_date" value="<?= e($filters['start_date'] ?? '') ?>"></label>
            <label>Fin avant<input type="date" name="end_date" value="<?= e($filters['end_date'] ?? '') ?>"></label>
            <button class="button compact" type="submit">Filtrer</button>
        </form>
    <?php elseif (($scope ?? '') === 'admin'): ?>
        <form class="filters panel" method="get">
            <label>Statut
                <select name="status">
                    <option value="">Tous les statuts</option>
                    <?php foreach (['pending_payment', 'confirmed', 'cancelled', 'completed'] as $status): ?>
                        <option value="<?= $status ?>" <?= input('status') === $status ? 'selected' : '' ?>><?= status_label($status) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <button class="button compact" type="submit">Filtrer</button>
        </form>
    <?php endif; ?>
    <?php require __DIR__ . '/_booking-table.php'; ?>
</section>
