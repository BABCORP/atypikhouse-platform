<?php

namespace App\Core;

final class Auth
{
    private static ?array $cachedUser = null;
    private static ?int $cachedUserId = null;

    public static function user(): ?array
    {
        if (empty($_SESSION['user_id'])) {
            self::$cachedUser = null;
            self::$cachedUserId = null;
            return null;
        }

        $userId = (int) $_SESSION['user_id'];
        if (self::$cachedUserId === $userId) {
            return self::$cachedUser;
        }

        $stmt = Database::connection()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([$userId]);
        $user = $stmt->fetch();

        self::$cachedUserId = $userId;
        self::$cachedUser = $user ?: null;

        return self::$cachedUser;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function requireLogin(): array
    {
        $user = self::user();
        if (!$user) {
            flash('error', 'Connectez-vous pour accéder à votre espace.');
            redirect('/connexion');
        }
        return $user;
    }

    public static function requireRole(array|string $roles): array
    {
        $user = self::requireLogin();
        $allowed = is_array($roles) ? $roles : [$roles];
        if (!in_array($user['role'], $allowed, true) && $user['role'] !== 'admin') {
            http_response_code(403);
            (new \App\Controllers\PublicController())->forbidden();
            exit;
        }
        return $user;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        self::$cachedUserId = (int) $user['id'];
        self::$cachedUser = $user;
    }

    public static function logout(): void
    {
        self::$cachedUser = null;
        self::$cachedUserId = null;
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
