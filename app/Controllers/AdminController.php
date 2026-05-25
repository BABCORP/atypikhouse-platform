<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Database;
use App\Models\AdminStats;
use App\Models\BlogPost;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Property;
use App\Models\Review;
use App\Models\User;

final class AdminController extends Controller
{
    public function dashboard(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/admin-dashboard', [
            'title' => 'Administration',
            'stats' => (new AdminStats())->dashboard(),
            'properties' => array_slice(array_filter((new Property())->allForAdmin(), fn (array $property): bool => $property['status'] === 'pending'), 0, 5),
            'bookings' => array_slice((new Booking())->all(), 0, 5),
            'reviews' => array_slice(array_filter((new Review())->all(), fn (array $review): bool => $review['status'] === 'pending'), 0, 5),
            'messages' => array_slice((new ContactMessage())->all(), 0, 5),
        ]);
    }

    public function users(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/users', ['title' => 'Utilisateurs', 'users' => (new User())->all(input('role') ?: null, input('status') ?: null)]);
    }

    public function updateUserStatus(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $status = (string) input('status');
        if (!in_array($status, ['active', 'pending', 'suspended'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
        (new User())->updateStatus($id, $status);
        audit((int) $admin['id'], 'user_status_update', 'user', $id);
        flash('success', 'Statut utilisateur mis à jour.');
        $this->redirect('/admin/utilisateurs');
    }

    public function owners(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/owners', ['title' => 'Propriétaires', 'owners' => (new User())->ownerProfiles()]);
    }

    public function updateOwnerStatus(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $status = (string) input('status');
        if (!in_array($status, ['approved', 'pending', 'rejected'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
        (new User())->updateOwnerProfileStatus($id, $status);
        audit((int) $admin['id'], 'owner_profile_moderation', 'owner_profile', $id);
        flash('success', 'Profil propriétaire mis à jour.');
        $this->redirect('/admin/proprietaires');
    }

    public function properties(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/admin-properties', ['title' => 'Logements', 'properties' => (new Property())->allForAdmin()]);
    }

    public function updatePropertyStatus(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $status = (string) input('status');
        if (!in_array($status, ['published', 'rejected', 'archived', 'pending'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
        (new Property())->updateStatus($id, $status);
        audit((int) $admin['id'], 'property_' . $status, 'property', $id);
        flash('success', 'Statut du logement mis à jour.');
        $this->redirect('/admin/logements');
    }

    public function bookings(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/bookings', ['title' => 'Toutes les réservations', 'bookings' => (new Booking())->all(), 'scope' => 'admin']);
    }

    public function updateBookingStatus(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $status = (string) input('status');
        if (!in_array($status, ['confirmed', 'cancelled', 'completed'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
        (new Booking())->updateStatus($id, $status);
        audit((int) $admin['id'], 'booking_status_update', 'booking', $id);
        flash('success', 'Réservation mise à jour.');
        $this->redirect('/admin/reservations');
    }

    public function reviews(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/admin-reviews', ['title' => 'Avis', 'reviews' => (new Review())->all()]);
    }

    public function updateReviewStatus(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $status = (string) input('status');
        if (!in_array($status, ['published', 'rejected'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
        (new Review())->updateStatus($id, $status);
        audit((int) $admin['id'], 'review_moderation', 'review', $id);
        flash('success', 'Avis modéré.');
        $this->redirect('/admin/avis');
    }

    public function deleteReview(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        (new Review())->delete($id);
        audit((int) $admin['id'], 'review_delete', 'review', $id);
        flash('success', 'Avis supprimé.');
        $this->redirect('/admin/avis');
    }

    public function blog(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/admin-blog', ['title' => 'Blog admin', 'posts' => (new BlogPost())->all()]);
    }

    public function storeBlog(): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $this->validateBlog();
        (new BlogPost())->create($_POST, (int) $admin['id']);
        flash('success', 'Article créé.');
        $this->redirect('/admin/blog');
    }

    public function editBlog(int $id): void
    {
        Auth::requireRole('admin');
        $post = (new BlogPost())->find($id);
        if (!$post) {
            http_response_code(404);
            exit('Article introuvable.');
        }
        $this->view('dashboard/admin-blog-edit', ['title' => 'Modifier un article', 'post' => $post]);
    }

    public function updateBlog(int $id): void
    {
        Auth::requireRole('admin');
        verify_csrf();
        $this->validateBlog();
        (new BlogPost())->update($id, $_POST);
        flash('success', 'Article mis à jour.');
        $this->redirect('/admin/blog');
    }

    public function deleteBlog(int $id): void
    {
        Auth::requireRole('admin');
        verify_csrf();
        (new BlogPost())->delete($id);
        flash('success', 'Article supprimé.');
        $this->redirect('/admin/blog');
    }

    public function messages(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/messages', ['title' => 'Messages', 'messages' => (new ContactMessage())->all()]);
    }

    public function updateMessageStatus(int $id): void
    {
        Auth::requireRole('admin');
        verify_csrf();
        $status = (string) input('status');
        if (!in_array($status, ['read', 'archived'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
        (new ContactMessage())->updateStatus($id, $status);
        flash('success', 'Message mis à jour.');
        $this->redirect('/admin/messages');
    }

    public function logs(): void
    {
        Auth::requireRole('admin');
        $logs = Database::connection()->query('SELECT al.*, u.email FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.created_at DESC LIMIT 200')->fetchAll();
        $this->view('dashboard/logs', ['title' => 'Journaux d’audit', 'logs' => $logs]);
    }

    private function validateBlog(): void
    {
        foreach (['title', 'excerpt', 'content'] as $field) {
            if (trim((string) input($field)) === '') {
                flash('error', 'Tous les champs de l’article sont obligatoires.');
                $this->redirect('/admin/blog');
            }
        }
        if (!in_array(input('status'), ['draft', 'published'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
    }
}
