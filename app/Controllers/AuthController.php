<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

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
        if ($user['status'] === 'suspended') {
            audit((int) $user['id'], 'blocked_suspended_login', 'user', (int) $user['id']);
            flash('error', 'Votre compte est suspendu.');
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
        audit($id, 'register_success', 'user', $id);
        flash('success', 'Votre compte a été créé. Vous pouvez vous connecter.');
        clear_old();
        $this->redirect('/connexion');
    }

    public function logout(): void
    {
        Auth::logout();
        redirect('/');
    }
}
