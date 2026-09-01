<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Booking;
use App\Models\Favorite;
use App\Models\Review;
use App\Models\User;

final class TenantController extends Controller
{
    public function dashboard(): void
    {
        $user = Auth::requireRole('tenant');
        $bookings = (new Booking())->tenantBookings((int) $user['id']);
        $this->view('dashboard/tenant-dashboard', [
            'title' => 'Espace locataire',
            'user' => $user,
            'bookings' => array_slice($bookings, 0, 4),
            'favorites' => array_slice((new Favorite())->propertiesForUser((int) $user['id']), 0, 3),
            'favoriteIds' => (new Favorite())->idsForUser((int) $user['id']),
        ]);
    }

    public function reservations(): void
    {
        $user = Auth::requireRole('tenant');
        $this->view('dashboard/bookings', ['title' => 'Mes réservations', 'bookings' => (new Booking())->tenantBookings((int) $user['id']), 'scope' => 'tenant']);
    }

    public function reservation(int $id): void
    {
        $user = Auth::requireRole('tenant');
        $booking = (new Booking())->findForTenant($id, (int) $user['id']);
        if (!$booking) {
            http_response_code(404);
            exit('Réservation introuvable.');
        }
        $this->view('dashboard/booking-detail', ['title' => 'Détail réservation', 'booking' => $booking, 'canReview' => (new Review())->canReview($id, (int) $user['id'])]);
    }

    public function reviews(): void
    {
        $user = Auth::requireRole('tenant');
        $bookingModel = new Booking();
        $this->view('dashboard/reviews', [
            'title' => 'Mes avis',
            'bookings' => $bookingModel->tenantBookings((int) $user['id']),
            'reviews' => (new Review())->forTenant((int) $user['id']),
        ]);
    }

    public function favorites(): void
    {
        $user = Auth::requireRole('tenant');
        $favorites = (new Favorite())->propertiesForUser((int) $user['id']);
        $this->view('dashboard/favorites', [
            'title' => 'Mes favoris',
            'properties' => $favorites,
            'favoriteIds' => array_map(static fn (array $property): int => (int) $property['id'], $favorites),
        ]);
    }

    public function profile(): void
    {
        $user = Auth::requireRole('tenant');
        $this->view('dashboard/profile', ['title' => 'Mon profil', 'user' => $user]);
    }

    public function updateProfile(): void
    {
        $user = Auth::requireRole('tenant');
        verify_csrf();
        (new User())->updateProfile((int) $user['id'], $_POST);
        flash('success', 'Profil mis à jour.');
        $this->redirect('/locataire/profil');
    }

    public function updatePassword(): void
    {
        $user = Auth::requireRole('tenant');
        verify_csrf();
        if (!password_verify((string) input('current_password'), $user['password_hash']) || strlen((string) input('password')) < 8 || input('password') !== input('password_confirmation')) {
            flash('error', 'La modification du mot de passe a échoué. Vérifiez les informations saisies.');
            $this->redirect('/locataire/profil');
        }
        (new User())->updatePassword((int) $user['id'], (string) input('password'));
        audit((int) $user['id'], 'password_update', 'user', (int) $user['id']);
        flash('success', 'Mot de passe mis à jour.');
        $this->redirect('/locataire/profil');
    }

    public function deleteAccount(): void
    {
        $user = Auth::requireRole('tenant');
        verify_csrf();
        if (($user['role'] ?? '') !== 'tenant') {
            http_response_code(403);
            exit('Accès refusé.');
        }
        if (!password_verify((string) input('current_password'), $user['password_hash'])) {
            flash('error', 'Mot de passe incorrect. Votre compte n’a pas été supprimé.');
            $this->redirect('/locataire/profil');
        }
        (new User())->anonymizeAndSuspend((int) $user['id']);
        audit((int) $user['id'], 'user_account_deleted', 'user', (int) $user['id']);
        unset($_SESSION['user_id']);
        session_regenerate_id(true);
        flash('success', 'Votre compte a été supprimé. Les informations nécessaires à l’historique du projet sont conservées sous forme anonymisée.');
        $this->redirect('/');
    }
}
