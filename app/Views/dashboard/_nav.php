<?php $user = \App\Core\Auth::user(); ?>
<nav class="dashboard-nav" aria-label="Navigation espace utilisateur">
    <?php if ($user['role'] === 'tenant'): ?>
        <a href="<?= url('/locataire/dashboard') ?>">Tableau de bord</a><a href="<?= url('/locataire/reservations') ?>">Réservations</a><a href="<?= url('/locataire/avis') ?>">Avis</a><a href="<?= url('/locataire/profil') ?>">Profil</a>
    <?php elseif ($user['role'] === 'owner'): ?>
        <a href="<?= url('/proprietaire/dashboard') ?>">Tableau de bord</a><a href="<?= url('/proprietaire/logements') ?>">Logements</a><a href="<?= url('/proprietaire/disponibilites') ?>">Disponibilités</a><a href="<?= url('/proprietaire/reservations') ?>">Réservations</a><a href="<?= url('/proprietaire/profil') ?>">Profil</a>
    <?php else: ?>
        <a href="<?= url('/admin/dashboard') ?>">Dashboard</a><a href="<?= url('/admin/utilisateurs') ?>">Utilisateurs</a><a href="<?= url('/admin/proprietaires') ?>">Propriétaires</a><a href="<?= url('/admin/logements') ?>">Logements</a><a href="<?= url('/admin/reservations') ?>">Réservations</a><a href="<?= url('/admin/avis') ?>">Avis</a><a href="<?= url('/admin/blog') ?>">Blog</a><a href="<?= url('/admin/messages') ?>">Messages</a><a href="<?= url('/admin/logs') ?>">Logs</a>
    <?php endif; ?>
</nav>
