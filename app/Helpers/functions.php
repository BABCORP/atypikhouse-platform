<?php

use App\Core\Database;

function env_value(string $key, mixed $default = null): mixed
{
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function config(string $key, mixed $default = null): mixed
{
    static $config = null;
    $config ??= require dirname(__DIR__, 2) . '/config/app.php';
    return $config[$key] ?? $default;
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = ''): string
{
    $path = '/' . ltrim($path, '/');
    return $path === '//' ? '/' : $path;
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function image_url(?string $path): string
{
    $path = $path ?: 'assets/img/properties/default-placeholder.svg';
    if (str_starts_with($path, 'media/')) {
        return url($path);
    }
    return asset(str_replace('assets/', '', $path));
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['_csrf'] ?? '';
    $sessionToken = $_SESSION['_csrf'] ?? '';
    if ($token === '' || $sessionToken === '' || !hash_equals($sessionToken, $token)) {
        http_response_code(419);
        exit('Jeton CSRF invalide.');
    }
}

function flash(string $key, ?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'][$key] = $message;
        return null;
    }

    $value = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $value;
}

function old(string $key, mixed $default = ''): string
{
    return e($_SESSION['_old'][$key] ?? $default);
}

function remember_old(array $data): void
{
    $_SESSION['_old'] = $data;
}

function clear_old(): void
{
    unset($_SESSION['_old']);
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    $text = preg_replace('~[^\\pL\\d]+~u', '-', $text ?: '');
    $text = trim((string) $text, '-');
    $text = strtolower($text);
    $text = preg_replace('~[^-a-z0-9]+~', '', $text);
    return $text ?: 'item-' . time();
}

function input(string $key, mixed $default = null): mixed
{
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

function audit(?int $userId, string $action, string $entityType, ?int $entityId = null): void
{
    try {
        $stmt = Database::connection()->prepare('INSERT INTO audit_logs (user_id, action, entity_type, entity_id, ip_address, created_at) VALUES (?, ?, ?, ?, ?, NOW())');
        $stmt->execute([$userId, $action, $entityType, $entityId, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);
    } catch (Throwable) {
        // Audit must never break the user journey.
    }
}

function money(float|int|string $amount): string
{
    return number_format((float) $amount, 2, ',', ' ') . ' EUR';
}

function rating_stars(float|int|string $rating, string $label = 'Note'): string
{
    $value = max(0, min(5, (float) $rating));
    $rounded = (int) round($value);
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        $stars .= '<span class="rating-stars__star' . ($i <= $rounded ? ' rating-stars__star--filled' : '') . '" aria-hidden="true">' . ($i <= $rounded ? '★' : '☆') . '</span>';
    }
    return '<span class="rating-stars" role="img" aria-label="' . e($label . ' : ' . number_format($value, 1, ',', ' ') . ' sur 5') . '">' . $stars . '</span>';
}

function nights_between(string $start, string $end): int
{
    try {
        return (int) (new DateTimeImmutable($start))->diff(new DateTimeImmutable($end))->days;
    } catch (Throwable) {
        return 0;
    }
}

function valid_date(string $date): bool
{
    $value = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $value !== false && $value->format('Y-m-d') === $date;
}

function status_label(?string $status): string
{
    return [
        'active' => 'Actif',
        'pending' => 'En attente',
        'rejected' => 'Refusé',
        'suspended' => 'Suspendu',
        'draft' => 'Brouillon',
        'published' => 'Publié',
        'rejected' => 'Refusé',
        'archived' => 'Archivé',
        'paused' => 'En pause',
        'deleted' => 'Supprimé',
        'pending_admin' => 'En attente de validation',
        'pending_payment' => 'En attente de paiement',
        'confirmed' => 'Confirmée',
        'cancelled' => 'Annulée',
        'completed' => 'Terminée',
        'not_paid' => 'Non payé',
        'test_paid' => 'Payé fictivement',
        'test_failed' => 'Paiement fictif échoué',
        'refunded' => 'Remboursé',
        'test_pending' => 'Paiement fictif en attente',
        'test_success' => 'Paiement fictif validé',
        'test_failed' => 'Paiement fictif échoué',
        'test_refunded' => 'Remboursement fictif',
        'approved' => 'Approuvé',
        'read' => 'Lu',
        'processed' => 'Traité',
        'new' => 'Nouveau',
    ][$status ?? ''] ?? (string) $status;
}

function role_label(?string $role): string
{
    return [
        'tenant' => 'Locataire',
        'owner' => 'Propriétaire',
        'admin' => 'Administrateur',
    ][$role ?? ''] ?? (string) $role;
}

function property_type_label(?string $type): string
{
    return [
        'treehouse' => 'Cabane dans les arbres',
        'yurt' => 'Yourte nature',
        'floating_cabin' => 'Cabane flottante',
        'tiny_house' => 'Tiny house',
        'dome' => 'Dôme',
        'other' => 'Autre insolite',
    ][$type ?? ''] ?? (string) $type;
}

function current_url(): string
{
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return request_origin() . url($path);
}

function request_origin(): string
{
    $configured = rtrim((string) config('base_url', ''), '/');
    $isConfiguredForLocal = str_contains($configured, 'localhost') || str_contains($configured, '127.0.0.1');
    $host = $_SERVER['HTTP_HOST'] ?? '';

    if ($configured !== '' && (!$isConfiguredForLocal || $host === '' || str_contains($host, 'localhost') || str_contains($host, '127.0.0.1'))) {
        return $configured;
    }

    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https'
        ? 'https'
        : 'http';

    return $host !== '' ? $scheme . '://' . $host : $configured;
}

function pagination_meta(int $total, int $page, int $perPage = 20): array
{
    $lastPage = max(1, (int) ceil($total / max(1, $perPage)));
    $page = min(max(1, $page), $lastPage);

    return [
        'total' => $total,
        'page' => $page,
        'per_page' => $perPage,
        'last_page' => $lastPage,
        'offset' => ($page - 1) * $perPage,
        'has_previous' => $page > 1,
        'has_next' => $page < $lastPage,
    ];
}

function pagination_url(int $page): string
{
    $params = $_GET;
    $params['page'] = $page;
    $path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    return url($path . '?' . http_build_query($params));
}
