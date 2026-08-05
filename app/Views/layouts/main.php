<?php
$currentUser = \App\Core\Auth::user();
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$isActivePath = static function (string $path) use ($currentPath): bool {
    return $path === '/' ? $currentPath === '/' : ($currentPath === $path || str_starts_with($currentPath, $path . '/'));
};
$navLinkClass = static fn (string $path): string => 'nav-link' . ($isActivePath($path) ? ' is-active' : '');
$ariaCurrent = static fn (string $path): string => $isActivePath($path) ? ' aria-current="page"' : '';
$canonicalUrl = isset($canonical) && is_string($canonical) && str_starts_with($canonical, '/')
    ? app_url($canonical)
    : ($canonical ?? current_url());
$breadcrumbs = $breadcrumbs ?? [];
if (!$breadcrumbs && $currentPath !== '/') {
    $breadcrumbLabels = [
        'concept' => 'Le concept',
        'hebergements' => 'Hébergements',
        'blog' => 'Blog',
        'faq' => 'FAQ',
        'devenir-hote' => 'Devenir hôte',
        'contact' => 'Contact',
        'connexion' => 'Connexion',
        'inscription' => 'Inscription',
        'mot-de-passe-oublie' => 'Mot de passe oublié',
        'mentions-legales' => 'Mentions légales',
        'cgu' => 'CGU',
        'cgv' => 'CGV',
        'politique-confidentialite' => 'Politique de confidentialité',
        'cookies' => 'Cookies',
        'plan-du-site' => 'Plan du site',
        'mes-donnees' => 'Mes données',
    ];
    $segments = array_values(array_filter(explode('/', trim($currentPath, '/'))));
    $path = '';
    foreach ($segments as $index => $segment) {
        $path .= '/' . $segment;
        $label = $breadcrumbLabels[$segment] ?? ucfirst(str_replace('-', ' ', $segment));
        $breadcrumbs[] = [
            'label' => $label,
            'url' => $index === array_key_last($segments) ? null : $path,
        ];
    }
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? config('name')) ?></title>
    <meta name="description" content="<?= e($metaDescription ?? 'AtypikHouse, marketplace fictive étudiante pour réserver des hébergements insolites et responsables.') ?>">
    <link rel="canonical" href="<?= e($canonicalUrl) ?>">
    <meta property="og:title" content="<?= e($title ?? config('name')) ?>">
    <meta property="og:description" content="<?= e($metaDescription ?? 'Projet étudiant fictif AtypikHouse.') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= e($canonicalUrl) ?>">
    <meta property="og:image" content="<?= e($ogImage ?? asset('img/properties/hero-atypikhouse.svg')) ?>">
    <link rel="icon" href="<?= url('/favicon.svg') ?>" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lora:wght@500;600;700&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/main.css') ?>">
    <script>
        window.dataLayer = window.dataLayer || [];
        window.ATYPIK_ANALYTICS = {
            ga4Id: <?= json_encode(config('ga4_id')) ?>,
            gtmId: <?= json_encode(config('gtm_id')) ?>
        };
    </script>
    <?php if (!empty($jsonLd)): ?>
        <script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
    <?php endif; ?>
    <?php if (!empty($breadcrumbs)): ?>
        <?php
        $breadcrumbJsonLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => array_map(static fn (array $item, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['label'],
                'item' => $item['url'] ? app_url($item['url']) : current_url(),
            ], $breadcrumbs, array_keys($breadcrumbs)),
        ];
        ?>
        <script type="application/ld+json"><?= json_encode($breadcrumbJsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
    <?php endif; ?>
    <?php if (captcha_is_configured()): ?>
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    <?php endif; ?>
</head>
<body>
<a class="skip-link" href="#contenu">Aller au contenu</a>
<header class="site-header" role="banner">
    <a class="brand" href="<?= url('/') ?>" aria-label="AtypikHouse, accueil">AtypikHouse</a>
    <button class="nav-toggle" type="button" aria-expanded="false" aria-controls="navigation-principale">Menu</button>
    <nav role="navigation" class="main-nav" id="navigation-principale" aria-label="Navigation principale">
        <a class="<?= e($navLinkClass('/')) ?>" href="<?= url('/') ?>"<?= $ariaCurrent('/') ?>>Accueil</a>
        <a class="<?= e($navLinkClass('/concept')) ?>" href="<?= url('/concept') ?>"<?= $ariaCurrent('/concept') ?>>Le concept</a>
        <a class="<?= e($navLinkClass('/hebergements')) ?>" href="<?= url('/hebergements') ?>"<?= $ariaCurrent('/hebergements') ?>>Hébergements</a>
        <a class="<?= e($navLinkClass('/blog')) ?>" href="<?= url('/blog') ?>"<?= $ariaCurrent('/blog') ?>>Blog</a>
        <a class="<?= e($navLinkClass('/faq')) ?>" href="<?= url('/faq') ?>"<?= $ariaCurrent('/faq') ?>>FAQ</a>
        <a class="<?= e($navLinkClass('/devenir-hote')) ?>" href="<?= url('/devenir-hote') ?>"<?= $ariaCurrent('/devenir-hote') ?>>Devenir hôte</a>
        <a class="<?= e($navLinkClass('/contact')) ?>" href="<?= url('/contact') ?>"<?= $ariaCurrent('/contact') ?>>Contact</a>
        <?php if ($currentUser): ?>
            <a class="button ghost" href="<?= url('/' . ($currentUser['role'] === 'admin' ? 'admin' : ($currentUser['role'] === 'owner' ? 'proprietaire' : 'locataire')) . '/dashboard') ?>">Mon espace</a>
            <form role="form" method="post" action="<?= url('/deconnexion') ?>"><?= csrf_field() ?><button class="link-button" type="submit">Déconnexion</button></form>
        <?php else: ?>
            <a class="button ghost" href="<?= url('/connexion') ?>">Connexion</a>
        <?php endif; ?>
    </nav>
</header>

<?php if ($message = flash('success')): ?><p class="flash success" role="status"><?= e($message) ?></p><?php endif; ?>
<?php if ($message = flash('warning')): ?><p class="flash warning" role="status"><?= e($message) ?></p><?php endif; ?>
<?php if ($message = flash('error')): ?><p class="flash error" role="alert"><?= e($message) ?></p><?php endif; ?>

<?php if (!empty($breadcrumbs)): ?>
    <nav class="breadcrumb" role="navigation" aria-label="Fil d’Ariane">
        <ol>
            <li><a href="<?= url('/') ?>">Accueil</a></li>
            <?php foreach ($breadcrumbs as $item): ?>
                <li>
                    <?php if (!empty($item['url'])): ?>
                        <a href="<?= url($item['url']) ?>"><?= e($item['label']) ?></a>
                    <?php else: ?>
                        <span aria-current="page"><?= e($item['label']) ?></span>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ol>
    </nav>
<?php endif; ?>

<main id="contenu" role="main">
    <?= $content ?>
</main>

<footer class="site-footer">
    <div class="footer-brand">
        <strong>AtypikHouse</strong>
        <p>Des séjours insolites et responsables pour renouer avec la nature.</p>
        <p class="disclaimer"><?= e(config('academic_disclaimer')) ?></p>
    </div>
    <div class="footer-column"><h2>Explorer</h2><nav role="navigation" aria-label="Explorer AtypikHouse"><a href="<?= url('/hebergements') ?>">Hébergements</a><a href="<?= url('/concept') ?>">Le concept</a><a href="<?= url('/devenir-hote') ?>">Devenir hôte</a><a href="<?= url('/contact') ?>">Contact</a></nav></div>
    <div class="footer-column"><h2>Suivez-nous</h2><nav role="navigation" aria-label="Réseaux sociaux AtypikHouse"><a href="https://www.instagram.com/atypikhouse__off/" target="_blank" rel="noopener noreferrer" aria-label="Suivre AtypikHouse sur Instagram">@atypikhouse__off</a></nav></div>
    <div class="footer-column"><h2>Informations</h2><nav role="navigation" aria-label="Liens légaux et accessibilité"><a href="<?= url('/plan-du-site') ?>">Plan du site</a><a href="<?= url('/mentions-legales') ?>">Mentions légales</a><a href="<?= url('/cgu') ?>">CGU</a><a href="<?= url('/cgv') ?>">CGV</a><a href="<?= url('/politique-confidentialite') ?>">Confidentialité</a><a href="<?= url('/mes-donnees') ?>">Mes données</a><a href="<?= url('/cookies') ?>">Cookies</a><button class="footer-cookie-control" type="button" data-cookie-manage>Gérer les cookies</button></nav></div>
</footer>
<div class="cookie-banner" data-cookie-banner role="dialog" aria-live="polite" aria-labelledby="cookie-banner-title" aria-describedby="cookie-banner-description">
    <div class="cookie-banner__content">
        <strong id="cookie-banner-title">Gestion des cookies</strong>
        <p id="cookie-banner-description">Nous utilisons des cookies nécessaires au fonctionnement du site. Avec votre accord, des cookies de mesure d’audience peuvent nous aider à comprendre la navigation et à améliorer l’expérience AtypikHouse.</p>
        <a class="cookie-banner__link" href="<?= url('/mentions-legales') ?>">En savoir plus dans les mentions légales</a>
    </div>
    <div class="cookie-actions">
        <button class="button compact" type="button" data-cookie-choice="accept">Accepter</button>
        <button class="button ghost compact" type="button" data-cookie-choice="refuse">Refuser</button>
    </div>
</div>
<button class="cookie-manage-floating" type="button" data-cookie-manage aria-label="Gérer les préférences cookies">Cookies</button>
<script src="<?= asset('js/main.js') ?>" defer></script>
</body>
</html>
