<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;
use App\Services\MailService;

final class AuthController extends Controller
{
    public function login(): void
    {
        $this->view('auth/login', [
            'title' => 'Connexion',
            'captchaRequired' => $this->loginCaptchaRequired(),
        ]);
    }

    public function authenticate(): void
    {
        verify_csrf();
        $attempts = $_SESSION['login_attempts'] ?? [];
        $attempts = array_values(array_filter($attempts, fn (int $timestamp): bool => $timestamp > time() - 300));
        if (count($attempts) >= 5) {
            flash('error', 'Trop de tentatives. Réessayez dans quelques minutes.');
            $this->redirect('/connexion');
        }
        $email = strtolower(trim((string) input('email')));
        if (count($attempts) >= 3 && !verify_captcha('login')) {
            flash('error', 'La vérification anti-spam est incorrecte. Merci de réessayer.');
            remember_old(['email' => $email]);
            $this->redirect('/connexion');
        }
        $password = trim((string) input('password'));
        $userModel = new User();
        $user = $userModel->findByEmail($email);
        if (!$user || !$this->passwordMatches($password, (string) $user['password_hash'])) {
            $attempts[] = time();
            $_SESSION['login_attempts'] = $attempts;
            audit($user['id'] ?? null, 'failed_login', 'user', $user['id'] ?? null);
            $this->logLoginFailure($email, $user);
            flash('error', 'Identifiants incorrects.');
            remember_old(['email' => $email]);
            $this->redirect('/connexion');
        }
        if (in_array($user['status'], ['suspended', 'rejected'], true)) {
            audit((int) $user['id'], 'blocked_inactive_login', 'user', (int) $user['id']);
            flash('error', $user['status'] === 'rejected' ? 'Votre compte a été refusé par l’administration.' : 'Votre compte est suspendu.');
            $this->redirect('/connexion');
        }
        unset($_SESSION['login_attempts']);
        Auth::login($user);
        audit((int) $user['id'], 'login_success', 'user', (int) $user['id']);
        $this->redirect(match ($user['role']) {
            'admin' => '/admin/dashboard',
            'owner' => '/proprietaire/dashboard',
            default => '/locataire/dashboard',
        });
    }

    public function register(): void
    {
        $this->view('auth/register', ['title' => 'Inscription', 'role' => input('role', 'tenant')]);
    }

    public function forgotPassword(): void
    {
        $this->view('auth/forgot-password', [
            'title' => 'Mot de passe oublié',
            'metaDescription' => 'Réinitialisation de mot de passe pour le projet étudiant AtypikHouse.',
        ]);
    }

    public function sendResetLink(): void
    {
        verify_csrf();
        $email = strtolower(trim((string) input('email')));
        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = (new User())->findByEmail($email);
            if ($user && $user['status'] !== 'suspended') {
                $token = bin2hex(random_bytes(32));
                (new User())->createPasswordReset((int) $user['id'], hash('sha256', $token));
                audit((int) $user['id'], 'password_reset_requested', 'user', (int) $user['id']);
                $resetUrl = app_url('/reinitialiser-mot-de-passe/' . $token);
                (new MailService())->sendPasswordResetDemo($email, $resetUrl);
                flash('demo_link', 'Lien de réinitialisation généré pour la démonstration académique : ' . $resetUrl);
            }
        }
        flash('success', 'Si un compte actif existe pour cette adresse, un lien de réinitialisation est disponible dans le cadre de la démonstration académique.');
        $this->redirect('/mot-de-passe-oublie');
    }

    public function resetPassword(string $token): void
    {
        $reset = (new User())->findPasswordReset(hash('sha256', $token));
        if (!$reset) {
            flash('error', 'Lien expiré ou invalide.');
            $this->redirect('/mot-de-passe-oublie');
        }
        $this->view('auth/reset-password', ['title' => 'Nouveau mot de passe', 'token' => $token]);
    }

    public function updateResetPassword(string $token): void
    {
        verify_csrf();
        $model = new User();
        $reset = $model->findPasswordReset(hash('sha256', $token));
        if (!$reset) {
            flash('error', 'Lien expiré ou invalide.');
            $this->redirect('/mot-de-passe-oublie');
        }
        if (strlen((string) input('password')) < 8 || input('password') !== input('password_confirmation')) {
            flash('error', 'Le mot de passe doit contenir au moins 8 caractères et être confirmé.');
            $this->redirect('/reinitialiser-mot-de-passe/' . $token);
        }
        $model->updatePassword((int) $reset['user_id'], (string) input('password'));
        $model->deletePasswordResets((int) $reset['user_id']);
        audit((int) $reset['user_id'], 'password_reset_completed', 'user', (int) $reset['user_id']);
        flash('success', 'Mot de passe mis à jour. Vous pouvez vous connecter.');
        $this->redirect('/connexion');
    }

    public function store(): void
    {
        verify_csrf();
        $role = input('role') === 'owner' ? 'owner' : 'tenant';
        if (!verify_captcha('register')) {
            flash('error', 'La vérification anti-spam est incorrecte. Merci de réessayer.');
            remember_old($_POST);
            $this->redirect('/inscription?role=' . $role);
        }
        foreach (['first_name', 'last_name', 'email', 'password'] as $field) {
            if (trim((string) input($field, '')) === '') {
                flash('error', 'Merci de remplir tous les champs obligatoires.');
                remember_old($_POST);
                $this->redirect('/inscription');
            }
        }
        if (!filter_var(input('email'), FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Adresse email invalide.');
            remember_old($_POST);
            $this->redirect('/inscription');
        }
        if (strlen((string) input('password')) < 8) {
            flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            remember_old($_POST);
            $this->redirect('/inscription');
        }
        $model = new User();
        if ($model->findByEmail((string) input('email'))) {
            flash('error', 'Un compte existe déjà avec cette adresse email.');
            remember_old($_POST);
            $this->redirect('/inscription');
        }
        $status = 'active';
        $id = $model->create([...$_POST, 'role' => $role, 'status' => $status]);
        if ($role === 'owner') {
            $model->createOwnerProfile($id, $_POST);
        }
        $mailService = new MailService();
        $email = strtolower(trim((string) input('email')));
        $firstName = trim((string) input('first_name'));
        audit($id, 'user_registered_active', 'user', $id);
        $emailSent = $mailService->sendTenantWelcomeNotification($email, $firstName);
        audit($id, $emailSent ? 'email_account_welcome_sent' : 'email_account_welcome_failed', 'user', $id);
        flash(
            $emailSent ? 'success' : 'warning',
            $emailSent
                ? 'Votre compte a bien été créé. Vous pouvez vous connecter dès maintenant. Un email de bienvenue vous a été envoyé.'
                : 'Votre compte a bien été créé. Vous pouvez vous connecter dès maintenant. L’email de bienvenue n’a pas pu être envoyé pour le moment.'
        );
        clear_old();
        $this->redirect('/connexion');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }

    private function passwordMatches(string $password, string $storedHash): bool
    {
        $storedHash = trim($storedHash);
        if ($storedHash === '') {
            return false;
        }

        if (password_verify($password, $storedHash)) {
            return true;
        }

        // Compatibility for old/demo rows that may have been imported before hashing was enforced.
        if (!str_starts_with($storedHash, '$2y$') && !str_starts_with($storedHash, '$argon') && hash_equals($storedHash, $password)) {
            return true;
        }

        return false;
    }

    private function loginCaptchaRequired(): bool
    {
        $attempts = $_SESSION['login_attempts'] ?? [];
        $attempts = array_values(array_filter($attempts, fn (int $timestamp): bool => $timestamp > time() - 300));
        $_SESSION['login_attempts'] = $attempts;

        return count($attempts) >= 3;
    }

    private function logLoginFailure(string $email, ?array $user): void
    {
        $payload = [
            'date' => date('c'),
            'event' => 'login_failed',
            'email' => $email,
            'reason' => $user ? 'password_mismatch' : 'email_not_found',
            'user_id' => $user['id'] ?? null,
            'status' => $user['status'] ?? null,
            'role' => $user['role'] ?? null,
            'hash_prefix' => isset($user['password_hash']) ? substr((string) $user['password_hash'], 0, 4) : null,
            'hash_length' => isset($user['password_hash']) ? strlen((string) $user['password_hash']) : null,
        ];

        error_log('[AtypikHouse auth] ' . json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
