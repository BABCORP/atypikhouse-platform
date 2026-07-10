<?php require __DIR__ . '/_nav.php'; ?>
<section class="section">
    <div class="section-heading">
        <div>
            <p class="eyebrow">Compte utilisateur</p>
            <h1><?= e($user['first_name'] . ' ' . $user['last_name']) ?></h1>
            <p><?= e($user['email']) ?></p>
        </div>
        <div class="actions-row">
            <a class="button ghost" href="<?= url('/admin/utilisateurs') ?>">Retour</a>
            <a class="button" href="<?= url('/admin/utilisateurs/' . $user['id'] . '/modifier') ?>">Modifier</a>
        </div>
    </div>
    <div class="dashboard-grid two">
        <article class="panel">
            <h2>Identité</h2>
            <dl class="detail-list">
                <dt>Rôle</dt><dd><span class="badge"><?= role_label($user['role']) ?></span></dd>
                <dt>Statut</dt><dd><span class="badge <?= e($user['status']) ?>"><?= status_label($user['status']) ?></span></dd>
                <dt>Téléphone</dt><dd><?= e($user['phone'] ?: 'Non renseigné') ?></dd>
                <dt>Créé le</dt><dd><?= e($user['created_at']) ?></dd>
                <dt>Mis à jour le</dt><dd><?= e($user['updated_at']) ?></dd>
            </dl>
            <div class="actions-row">
                <?php if ($user['status'] !== 'active'): ?>
                    <form method="post" action="<?= url('/admin/utilisateurs/' . $user['id'] . '/approuver') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Approuver</button></form>
                <?php endif; ?>
                <?php if ($user['status'] !== 'rejected'): ?>
                    <form method="post" action="<?= url('/admin/utilisateurs/' . $user['id'] . '/refuser') ?>" data-confirm="Refuser ce compte utilisateur ?"><?= csrf_field() ?><button class="button ghost compact" type="submit">Refuser</button></form>
                <?php endif; ?>
                <?php if ($user['status'] === 'suspended'): ?>
                    <form method="post" action="<?= url('/admin/utilisateurs/' . $user['id'] . '/reactiver') ?>"><?= csrf_field() ?><button class="button compact" type="submit">Réactiver</button></form>
                <?php else: ?>
                    <form method="post" action="<?= url('/admin/utilisateurs/' . $user['id'] . '/suspendre') ?>" data-confirm="Suspendre ce compte utilisateur ?"><?= csrf_field() ?><button class="button danger compact" type="submit">Suspendre</button></form>
                <?php endif; ?>
            </div>
        </article>
        <?php if (!empty($ownerProfile)): ?>
            <article class="panel">
                <h2>Profil hôte</h2>
                <dl class="detail-list">
                    <dt>Société</dt><dd><?= e($ownerProfile['company_name']) ?></dd>
                    <dt>Ville</dt><dd><?= e($ownerProfile['city']) ?></dd>
                    <dt>Validation</dt><dd><span class="badge <?= e($ownerProfile['verification_status']) ?>"><?= status_label($ownerProfile['verification_status']) ?></span></dd>
                    <dt>Description</dt><dd><?= e($ownerProfile['description']) ?></dd>
                </dl>
            </article>
        <?php endif; ?>
    </div>
    <?php if (!empty($properties)): ?>
        <h2>Logements du propriétaire</h2>
        <?php $scope = 'admin'; require __DIR__ . '/_property-table.php'; ?>
    <?php endif; ?>
    <?php if (!empty($bookings)): ?>
        <h2>Réservations du locataire</h2>
        <?php $scope = 'admin'; require __DIR__ . '/_booking-table.php'; ?>
    <?php endif; ?>
</section>
