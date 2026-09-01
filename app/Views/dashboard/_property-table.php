<div class="table-wrap"><table>
    <caption>Logements et statuts de publication</caption>
    <thead><tr><th>Titre</th><th>Ville</th><th>Type</th><th>Prix</th><th>Statut</th><th>Actions</th></tr></thead>
    <tbody>
    <?php foreach (($properties ?? []) as $property): ?>
        <tr>
            <td><?= e($property['title']) ?></td>
            <td><?= e($property['city']) ?></td>
            <td><?= property_type_label($property['type']) ?></td>
            <td><?= money($property['price_per_night']) ?></td>
            <td>
                <span class="badge <?= e($property['status']) ?>"><?= status_label($property['status']) ?></span>
                <?php if (($scope ?? '') === 'admin' && ($property['pending_change_count'] ?? 0) > 0): ?>
                    <span class="badge pending">Ancienne modification en attente</span>
                <?php elseif (($scope ?? '') === 'admin' && ($property['latest_change_status'] ?? '') === 'rejected'): ?>
                    <span class="badge rejected">Modification refusée</span>
                    <?php if (!empty($property['latest_change_reason'])): ?><small>Motif : <?= e($property['latest_change_reason']) ?></small><?php endif; ?>
                <?php endif; ?>
            </td>
            <td class="table-actions">
                <?php if (($scope ?? '') === 'admin'): ?>
                    <a class="button compact ghost" href="<?= url('/admin/logements/' . $property['id']) ?>">Voir</a>
                    <?php if ($property['status'] !== 'deleted'): ?>
                        <?php if (in_array($property['status'], ['pending', 'rejected', 'draft'], true)): ?><form role="form" method="post" class="inline-form" action="<?= url('/admin/logements/' . $property['id'] . '/approuver') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Publier</button></form><?php endif; ?>
                        <?php if ($property['status'] === 'pending'): ?><form role="form" method="post" class="inline-form" action="<?= url('/admin/logements/' . $property['id'] . '/refuser') ?>" data-confirm="Refuser ce logement ?"><?= csrf_field() ?><button class="button ghost compact" type="submit">Refuser</button></form><?php endif; ?>
                        <?php if ($property['status'] === 'published'): ?><form role="form" method="post" class="inline-form" action="<?= url('/admin/logements/' . $property['id'] . '/mettre-en-pause') ?>" data-confirm="Mettre ce logement en pause ?"><?= csrf_field() ?><button class="button ghost compact" type="submit">Pause</button></form><?php endif; ?>
                        <?php if ($property['status'] === 'paused'): ?><form role="form" method="post" class="inline-form" action="<?= url('/admin/logements/' . $property['id'] . '/reactiver') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Réactiver</button></form><?php endif; ?>
                    <?php endif; ?>
                <?php elseif (($scope ?? '') !== 'admin' && $property['status'] !== 'deleted'): ?>
                    <a class="button compact ghost" href="<?= url('/proprietaire/logements/' . $property['id'] . '/modifier') ?>">Modifier</a>
                    <?php if (in_array($property['status'], ['draft', 'rejected'], true)): ?><form role="form" method="post" class="inline-form" action="<?= url('/proprietaire/logements/' . $property['id'] . '/soumettre') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Soumettre</button></form><?php endif; ?>
                    <?php if (in_array($property['status'], ['draft', 'rejected'], true)): ?><form role="form" method="post" class="inline-form" action="<?= url('/proprietaire/logements/' . $property['id'] . '/supprimer') ?>" data-confirm="Supprimer ce brouillon de logement ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Supprimer</button></form><?php endif; ?>
                <?php endif; ?>
                <?php if (($scope ?? '') !== 'admin' && ($property['status'] ?? '') === 'paused'): ?>
                    <small>Mis en pause par l’administrateur. Contactez l’équipe AtypikHouse.</small>
                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($properties)): ?>
        <tr><td colspan="6" class="empty-state">Aucun logement à afficher.</td></tr>
    <?php endif; ?>
    </tbody>
</table></div>
