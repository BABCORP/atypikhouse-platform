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
        $page = max(1, (int) input('page', 1));
        $result = (new User())->paginated(input('role') ?: null, input('status') ?: null, 20, ($page - 1) * 20);
        $this->view('dashboard/users', ['title' => 'Utilisateurs', 'users' => $result['items'], 'pagination' => pagination_meta($result['total'], $page)]);
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
        $model = new User();
        $user = $model->find($id);
        if ($user && $user['role'] === 'admin' && $user['status'] === 'active' && $status !== 'active' && $model->activeAdminCount() <= 1) {
            flash('error', 'Impossible de suspendre le dernier administrateur actif.');
            $this->redirect('/admin/utilisateurs');
        }
        $model->updateStatus($id, $status);
        audit((int) $admin['id'], 'user_status_update', 'user', $id);
        flash('success', 'Statut utilisateur mis à jour.');
        $this->redirect('/admin/utilisateurs');
    }

    public function editUser(int $id): void
    {
        Auth::requireRole('admin');
        $user = (new User())->find($id);
        if (!$user) {
            http_response_code(404);
            exit('Utilisateur introuvable.');
        }
        $this->view('dashboard/user-edit', ['title' => 'Modifier un utilisateur', 'editUser' => $user]);
    }

    public function updateUser(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            http_response_code(404);
            exit('Utilisateur introuvable.');
        }
        $role = (string) input('role');
        $status = (string) input('status');
        if (!in_array($role, ['tenant', 'owner', 'admin'], true) || !in_array($status, ['active', 'pending', 'suspended'], true)) {
            flash('error', 'Rôle ou statut invalide.');
            $this->redirect('/admin/utilisateurs/' . $id . '/modifier');
        }
        if (!filter_var(input('email'), FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Adresse email invalide.');
            $this->redirect('/admin/utilisateurs/' . $id . '/modifier');
        }
        if ($model->emailExistsForOther((string) input('email'), $id)) {
            flash('error', 'Cette adresse email est déjà utilisée.');
            $this->redirect('/admin/utilisateurs/' . $id . '/modifier');
        }
        $wouldRemoveLastAdmin = $user['role'] === 'admin'
            && $user['status'] === 'active'
            && ($role !== 'admin' || $status !== 'active')
            && $model->activeAdminCount() <= 1;
        if ($wouldRemoveLastAdmin) {
            flash('error', 'Impossible de retirer, suspendre ou rétrograder le dernier administrateur actif.');
            $this->redirect('/admin/utilisateurs/' . $id . '/modifier');
        }
        $model->updateAdminUser($id, $_POST);
        audit((int) $admin['id'], 'user_update', 'user', $id);
        if ($user['role'] !== $role) {
            audit((int) $admin['id'], 'user_role_update', 'user', $id);
        }
        if ($user['status'] !== $status) {
            audit((int) $admin['id'], 'user_status_update', 'user', $id);
        }
        flash('success', 'Utilisateur mis à jour.');
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
        $page = max(1, (int) input('page', 1));
        $result = (new Booking())->paginatedAll(['status' => input('status') ?: null], 20, ($page - 1) * 20);
        $this->view('dashboard/bookings', ['title' => 'Toutes les réservations', 'bookings' => $result['items'], 'pagination' => pagination_meta($result['total'], $page), 'scope' => 'admin']);
    }

    public function bookingDetail(int $id): void
    {
        Auth::requireRole('admin');
        $booking = (new Booking())->findForAdmin($id);
        if (!$booking) {
            http_response_code(404);
            exit('Réservation introuvable.');
        }
        $this->view('dashboard/admin-booking-detail', ['title' => 'Réservation #' . $id, 'booking' => $booking]);
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
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/reservations');
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
        $type = in_array(input('type'), ['contact', 'newsletter', 'rgpd'], true) ? (string) input('type') : 'all';
        $page = max(1, (int) input('page', 1));
        $result = (new ContactMessage())->paginated($type, 20, ($page - 1) * 20);
        $this->view('dashboard/messages', ['title' => 'Messages', 'messages' => $result['items'], 'type' => $type, 'pagination' => pagination_meta($result['total'], $page)]);
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
        $page = max(1, (int) input('page', 1));
        $perPage = 20;
        $where = ' WHERE 1=1';
        $params = [];
        if (trim((string) input('action', '')) !== '') {
            $where .= ' AND al.action LIKE ?';
            $params[] = '%' . trim((string) input('action')) . '%';
        }
        if (trim((string) input('email', '')) !== '') {
            $where .= ' AND u.email LIKE ?';
            $params[] = '%' . trim((string) input('email')) . '%';
        }
        if (valid_date((string) input('start_date', ''))) {
            $where .= ' AND DATE(al.created_at) >= ?';
            $params[] = input('start_date');
        }
        if (valid_date((string) input('end_date', ''))) {
            $where .= ' AND DATE(al.created_at) <= ?';
            $params[] = input('end_date');
        }
        $count = Database::connection()->prepare('SELECT COUNT(*) FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id' . $where);
        $count->execute($params);
        $pagination = pagination_meta((int) $count->fetchColumn(), $page, $perPage);
        $stmt = Database::connection()->prepare('SELECT al.*, u.email FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id' . $where . ' ORDER BY al.created_at DESC LIMIT ' . $perPage . ' OFFSET ' . $pagination['offset']);
        $stmt->execute($params);
        $this->view('dashboard/logs', ['title' => 'Journaux d’audit', 'logs' => $stmt->fetchAll(), 'pagination' => $pagination]);
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
