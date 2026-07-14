<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Planning propriétaire</p>
            <h1>Gérer les disponibilités</h1>
            <p>Définissez des plages disponibles ou indisponibles, avec prix spécifique optionnel pour la réservation de démonstration.</p>
        </div>
    </div>
    <form class="panel availability-property-switch" method="get">
        <label>Logement affiché dans le calendrier
            <select name="property_id" onchange="this.form.submit()">
                <?php foreach ($properties as $property): ?>
                    <option value="<?= (int) $property['id'] ?>" <?= ($selectedPropertyId ?? null) === (int) $property['id'] ? 'selected' : '' ?>><?= e($property['title']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <noscript><button class="button compact" type="submit">Afficher</button></noscript>
    </form>
    <div class="dashboard-grid two">
        <form class="panel" method="post">
            <?= csrf_field() ?>
            <label>Logement
                <select name="property_id" required>
                    <?php foreach ($properties as $property): ?>
                        <option value="<?= (int) $property['id'] ?>" <?= ($selectedPropertyId ?? null) === (int) $property['id'] ? 'selected' : '' ?>><?= e($property['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <div class="form-grid">
                <label>Date de début<input required type="date" name="start_date" min="<?= date('Y-m-d') ?>"></label>
                <label>Date de fin<input required type="date" name="end_date" min="<?= date('Y-m-d') ?>"></label>
            </div>
            <label>Statut de la plage
                <select name="is_available">
                    <option value="1">Disponible</option>
                    <option value="0">Indisponible</option>
                </select>
            </label>
            <label>Prix spécifique optionnel par nuit<input type="number" min="0" step="0.01" name="price_override" placeholder="Ex. 180"></label>
            <p class="notice">La plage est limitée à 90 jours. Le prix spécifique est utilisé dans le total de réservation nuit par nuit.</p>
            <button class="button" type="submit">Enregistrer la plage</button>
        </form>
        <aside class="panel availability-calendar-panel">
            <div class="calendar-toolbar">
                <button class="button ghost compact" type="button" data-calendar-prev>Mois précédent</button>
                <h2 data-calendar-title>Calendrier</h2>
                <button class="button ghost compact" type="button" data-calendar-next>Mois suivant</button>
            </div>
            <div class="calendar-legend" aria-label="Légende des disponibilités">
                <span><i class="legend-dot available"></i>Disponible</span>
                <span><i class="legend-dot unavailable"></i>Indisponible</span>
                <span><i class="legend-dot override"></i>Prix spécifique</span>
                <span><i class="legend-dot booked"></i>Réservé</span>
            </div>
            <div class="availability-calendar" data-availability-calendar data-calendar-payload="<?= e(json_encode($calendarData ?? [], JSON_UNESCAPED_UNICODE)) ?>" aria-live="polite"></div>
            <p class="notice">Les dates réservées correspondent aux réservations confirmées ou terminées et ne sont pas éditables depuis le planning.</p>
        </aside>
    </div>
    <div class="dashboard-grid two">
        <aside class="panel">
            <h2>Prochaines règles</h2>
            <?php if (empty($availabilities)): ?>
                <p class="empty-state">Aucune disponibilité spécifique enregistrée pour ce logement.</p>
            <?php else: ?>
                <div class="table-wrap compact-table"><table>
                    <caption>Disponibilités à venir du logement sélectionné</caption>
                    <thead><tr><th>Date</th><th>Disponibilité</th><th>Prix spécifique</th></tr></thead>
                    <tbody>
                    <?php foreach ($availabilities as $availability): ?>
                        <tr>
                            <td><?= e($availability['date']) ?></td>
                            <td><span class="badge <?= (int) $availability['is_available'] === 1 ? 'published' : 'cancelled' ?>"><?= (int) $availability['is_available'] === 1 ? 'Disponible' : 'Indisponible' ?></span></td>
                            <td><?= $availability['price_override'] !== null ? money($availability['price_override']) : 'Prix standard' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table></div>
            <?php endif; ?>
        </aside>
    </div>
</section>
