<?php if (!empty($pagination) && ($pagination['last_page'] ?? 1) > 1): ?>
    <nav role="navigation" class="pagination" aria-label="Pagination">
        <?php if ($pagination['has_previous']): ?>
            <a class="button ghost compact" href="<?= pagination_url((int) $pagination['page'] - 1) ?>">Précédent</a>
        <?php else: ?>
            <span class="button ghost compact disabled" aria-disabled="true">Précédent</span>
        <?php endif; ?>
        <span>Page <?= (int) $pagination['page'] ?> / <?= (int) $pagination['last_page'] ?></span>
        <?php if ($pagination['has_next']): ?>
            <a class="button ghost compact" href="<?= pagination_url((int) $pagination['page'] + 1) ?>">Suivant</a>
        <?php else: ?>
            <span class="button ghost compact disabled" aria-disabled="true">Suivant</span>
        <?php endif; ?>
    </nav>
<?php endif; ?>
