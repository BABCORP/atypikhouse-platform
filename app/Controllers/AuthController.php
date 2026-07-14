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
        $this->view('auth/login', ['title' => 'Connexion']);
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
        $user = (new User())->findByEmail((string) input('email'));
        if (!$user || !password_verify((string) input('password'), $user['password_hash'])) {
            $attempts[] = time();
            $_SESSION['login_attempts'] = $attempts;
            audit($user['id'] ?? null, 'failed_login', 'user', $user['id'] ?? null);
            flash('error', 'Identifiants incorrects.');
            remember_old(['email' => input('email')]);
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
                $resetUrl = url('/reinitialiser-mot-de-passe/' . $token);
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
        $id = $model->create([...$_POST, 'role' => $role]);
        if ($role === 'owner') {
            $model->createOwnerProfile($id, $_POST);
        }
        audit($id, 'user_registered_pending', 'user', $id);
        (new MailService())->sendAccountPendingNotification(
            strtolower(trim((string) input('email'))),
            trim((string) input('first_name')),
            $role
        );
        flash('success', 'Votre compte a été créé et attend la validation de l’administrateur.');
        clear_old();
        $this->redirect('/connexion');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }
}
