<?php $currentUser = \App\Core\Auth::user(); ?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? config('name')) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'AtypikHouse, marketplace fictive étudiante pour réserver des hébergements insolites et responsables.') ?>">
    <meta property="og:title" content="<?= e($title ?? config('name')) ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? 'Projet étudiant fictif AtypikHouse.') ?>">
    <meta property="og:type" content="website">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <?php if (config('gtm_id')): ?>
        <script>window.dataLayer=window.dataLayer||[];</script>
    <?php endif; ?>
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="site-header">
    <a class="brand" href="<?= url('/') ?>">AtypikHouse</a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="navigation-principale">Menu</button>
    <nav class="main-nav" id="navigation-principale" aria-label="Navigation principale">
        <a href="<?= url('/') ?>">Accueil</a>
        <a href="<?= url('/concept') ?>">Le concept</a>
        <a href="<?= url('/hebergements') ?>">Hébergements</a>
        <a href="<?= url('/blog') ?>">Blog</a>
        <a href="<?= url('/faq') ?>">FAQ</a>
        <a href="<?= url('/devenir-hote') ?>">Devenir hôte</a>
        <a href="<?= url('/contact') ?>">Contact</a>
        <?php if ($currentUser): ?>
            <a class="button ghost" href="<?= url('/' . ($currentUser['role'] === 'admin' ? 'admin' : ($currentUser['role'] === 'owner' ? 'proprietaire' : 'locataire')) . '/dashboard') ?>">Mon espace</a>
            <form method="post" action="<?= url('/deconnexion') ?>"><?= csrf_field() ?><button class="link-button" type="submit">Déconnexion</button></form>
        <?php else: ?>
            <a class="button ghost" href="<?= url('/connexion') ?>">Connexion</a>
        <?php endif; ?>
    </nav>
</header>

<?php if ($message = flash('success')): ?><p class="flash success" role="status"><?= e($message) ?></p><?php endif; ?>
<?php if ($message = flash('error')): ?><p class="flash error" role="alert"><?= e($message) ?></p><?php endif; ?>

<main id="contenu">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div>
        <strong>AtypikHouse</strong>
        <p>SARL fictive - 12 rue du Château, 60350 Pierrefonds, France.</p>
        <p class="disclaimer"><?= e(config('academic_disclaimer')) ?></p>
    </div>
    <nav aria-label="Liens légaux">
        <a href="<?= url('/mentions-legales') ?>">Mentions légales</a>
        <a href="<?= url('/cgu') ?>">CGU</a>
        <a href="<?= url('/cgv') ?>">CGV</a>
        <a href="<?= url('/politique-confidentialite') ?>">Confidentialité</a>
        <a href="<?= url('/cookies') ?>">Cookies</a>
        <a href="<?= url('/faq') ?>">FAQ</a>
    </nav>
</footer>
<script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
