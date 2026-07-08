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
        $logs = Database::connection()->query('SELECT al.*, u.email FROM audit_logs al LEFT JOIN users u ON u.id = al.user_id ORDER BY al.created_at DESC LIMIT 6')->fetchAll();
        $this->view('dashboard/admin-dashboard', [
            'title' => 'Administration',
            'stats' => (new AdminStats())->dashboard(),
            'properties' => array_slice(array_filter((new Property())->allForAdmin(), fn (array $property): bool => $property['status'] === 'pending'), 0, 5),
            'bookings' => array_slice((new Booking())->all(), 0, 5),
            'reviews' => array_slice(array_filter((new Review())->all(), fn (array $review): bool => $review['status'] === 'pending'), 0, 5),
            'messages' => array_slice((new ContactMessage())->all(), 0, 5),
            'logs' => $logs,
        ]);
    }

    public function users(): void
    {
        Auth::requireRole('admin');
        $page = max(1, (int) input('page', 1));
        $result = (new User())->paginated(input('role') ?: null, input('status') ?: null, 20, ($page - 1) * 20, input('search') ?: null);
        $this->view('dashboard/users', ['title' => 'Utilisateurs', 'users' => $result['items'], 'pagination' => pagination_meta($result['total'], $page)]);
    }

    public function userDetail(int $id): void
    {
        Auth::requireRole('admin');
        $userModel = new User();
        $user = $userModel->find($id);
        if (!$user) {
            http_response_code(404);
            exit('Utilisateur introuvable.');
        }
        $properties = $user['role'] === 'owner' ? (new Property())->ownerProperties($id) : [];
        $bookings = $user['role'] === 'tenant' ? (new Booking())->tenantBookings($id) : [];
        $this->view('dashboard/user-detail', [
            'title' => 'Utilisateur #' . $id,
            'user' => $user,
            'ownerProfile' => $user['role'] === 'owner' ? $userModel->ownerProfile($id) : null,
            'properties' => $properties,
            'bookings' => $bookings,
        ]);
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

    public function suspendUser(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $model = new User();
        $user = $model->find($id);
        if (!$user) {
            http_response_code(404);
            exit('Utilisateur introuvable.');
        }
        if ($user['role'] === 'admin' && $user['status'] === 'active' && $model->activeAdminCount() <= 1) {
            flash('error', 'Impossible de suspendre le dernier administrateur actif.');
            $this->redirect('/admin/utilisateurs');
        }
        $model->updateStatus($id, 'suspended');
        audit((int) $admin['id'], 'user_suspended', 'user', $id);
        flash('success', 'Utilisateur suspendu.');
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/utilisateurs');
    }

    public function reactivateUser(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        (new User())->updateStatus($id, 'active');
        audit((int) $admin['id'], 'user_reactivated', 'user', $id);
        flash('success', 'Utilisateur réactivé.');
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/utilisateurs');
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
        $status = in_array(input('status'), ['approved', 'pending', 'rejected'], true) ? (string) input('status') : null;
        $this->view('dashboard/owners', ['title' => 'Propriétaires', 'owners' => (new User())->ownerProfiles($status, input('search') ?: null)]);
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
        $page = max(1, (int) input('page', 1));
        $filters = [
            'status' => in_array(input('status'), ['draft', 'pending', 'published', 'rejected', 'archived', 'paused', 'deleted'], true) ? input('status') : null,
            'type' => in_array(input('type'), ['treehouse', 'yurt', 'floating_cabin', 'tiny_house', 'dome', 'other'], true) ? input('type') : null,
            'owner' => input('owner') ?: null,
            'location' => input('location') ?: null,
            'search' => input('search') ?: null,
        ];
        $result = (new Property())->paginatedForAdmin($filters, 20, ($page - 1) * 20);
        $this->view('dashboard/admin-properties', ['title' => 'Logements', 'properties' => $result['items'], 'pagination' => pagination_meta($result['total'], $page)]);
    }

    public function propertyDetail(int $id): void
    {
        Auth::requireRole('admin');
        $model = new Property();
        $property = $model->findForAdmin($id);
        if (!$property) {
            http_response_code(404);
            exit('Logement introuvable.');
        }
        $this->view('dashboard/admin-property-detail', [
            'title' => 'Logement #' . $id,
            'property' => $property,
            'images' => $model->images($id),
            'amenities' => $model->amenities($id),
            'bookingCount' => $model->bookingCount($id),
        ]);
    }

    public function editProperty(int $id): void
    {
        Auth::requireRole('admin');
        $model = new Property();
        $property = $model->findForAdmin($id);
        if (!$property) {
            http_response_code(404);
            exit('Logement introuvable.');
        }
        $this->view('dashboard/admin-property-edit', ['title' => 'Modifier un logement', 'property' => $property, 'amenities' => implode(', ', $model->amenities($id))]);
    }

    public function updateProperty(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $this->validateProperty();
        (new Property())->updateAdmin($id, $_POST);
        audit((int) $admin['id'], 'property_updated_by_admin', 'property', $id);
        flash('success', 'Logement mis à jour par l’administration.');
        $this->redirect('/admin/logements/' . $id);
    }

    public function updatePropertyStatus(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $status = (string) input('status');
        if (!in_array($status, ['published', 'rejected', 'archived', 'pending', 'paused', 'deleted'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
        (new Property())->updateStatus($id, $status);
        $action = ['published' => 'property_approved', 'rejected' => 'property_rejected', 'archived' => 'property_disabled', 'paused' => 'property_paused', 'deleted' => 'property_deleted', 'pending' => 'property_submitted'][$status] ?? ('property_' . $status);
        audit((int) $admin['id'], $action, 'property', $id);
        flash('success', 'Statut du logement mis à jour.');
        $this->redirect('/admin/logements');
    }

    public function approveProperty(int $id): void
    {
        $this->quickPropertyStatus($id, 'published', 'property_approved', 'Le logement a été approuvé et publié dans le catalogue.', '/admin/logements/' . $id);
    }

    public function rejectProperty(int $id): void
    {
        $this->quickPropertyStatus($id, 'rejected', 'property_rejected', 'Le logement a été refusé et reste invisible publiquement.', '/admin/logements/' . $id);
    }

    public function disableProperty(int $id): void
    {
        $this->quickPropertyStatus($id, 'paused', 'property_paused', 'Le logement a été mis en pause. Il n’est plus visible dans le catalogue public.', '/admin/logements/' . $id);
    }

    public function pauseProperty(int $id): void
    {
        $this->quickPropertyStatus($id, 'paused', 'property_paused', 'Le logement a été mis en pause. Il n’est plus visible dans le catalogue public.', '/admin/logements/' . $id);
    }

    public function reactivateProperty(int $id): void
    {
        Auth::requireRole('admin');
        $property = (new Property())->find($id);
        if (!$property) {
            http_response_code(404);
            exit('Logement introuvable.');
        }
        if ($property['status'] !== 'paused') {
            flash('error', 'Seuls les logements en pause peuvent être réactivés directement.');
            $this->redirect('/admin/logements');
        }
        $this->quickPropertyStatus($id, 'published', 'property_reactivated', 'Le logement a été réactivé et est de nouveau visible dans le catalogue public.', '/admin/logements/' . $id);
    }

    public function deleteProperty(int $id): void
    {
        $this->quickPropertyStatus($id, 'deleted', 'property_deleted', 'Le logement a été supprimé du catalogue. Son historique reste conservé dans le back-office.', '/admin/logements');
    }

    public function bookings(): void
    {
        Auth::requireRole('admin');
        $page = max(1, (int) input('page', 1));
        $result = (new Booking())->paginatedAll([
            'status' => input('status') ?: null,
            'payment_status' => input('payment_status') ?: null,
            'property' => input('property') ?: null,
            'owner' => input('owner') ?: null,
            'tenant' => input('tenant') ?: null,
            'start_date' => input('start_date') ?: null,
            'end_date' => input('end_date') ?: null,
        ], 20, ($page - 1) * 20);
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
        $propertyModel = new Property();
        $availabilities = $propertyModel->availabilities((int) $booking['property_id']);
        $this->view('dashboard/admin-booking-detail', [
            'title' => 'Réservation #' . $id,
            'booking' => $booking,
            'calendarData' => [
                'availabilities' => array_map(static fn (array $row): array => [
                    'date' => $row['date'],
                    'is_available' => (int) $row['is_available'],
                    'price_override' => $row['price_override'] !== null ? (float) $row['price_override'] : null,
                ], $availabilities),
                'booked_dates' => (new Booking())->bookedDatesForProperty((int) $booking['property_id']),
            ],
        ]);
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
        $action = ['cancelled' => 'booking_cancelled_by_admin', 'completed' => 'booking_completed_by_admin'][$status] ?? 'booking_status_update';
        audit((int) $admin['id'], $action, 'booking', $id);
        flash('success', 'Réservation mise à jour.');
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/reservations');
    }

    public function cancelBooking(int $id): void
    {
        $this->quickBookingStatus($id, 'cancelled', 'booking_cancelled_by_admin', 'Réservation annulée.');
    }

    public function completeBooking(int $id): void
    {
        $this->quickBookingStatus($id, 'completed', 'booking_completed_by_admin', 'Réservation marquée comme terminée.');
    }

    public function reviews(): void
    {
        Auth::requireRole('admin');
        $this->view('dashboard/admin-reviews', ['title' => 'Avis', 'reviews' => (new Review())->filtered([
            'status' => input('status') ?: null,
            'rating' => input('rating') ?: null,
            'property' => input('property') ?: null,
            'author' => input('author') ?: null,
        ])]);
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
        audit((int) $admin['id'], $status === 'published' ? 'review_approved' : 'review_rejected', 'review', $id);
        flash('success', 'Avis modéré.');
        $this->redirect('/admin/avis');
    }

    public function deleteReview(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        (new Review())->delete($id);
        audit((int) $admin['id'], 'review_deleted', 'review', $id);
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
        audit((int) $admin['id'], 'blog_created', 'blog_post', null);
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
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $this->validateBlog();
        (new BlogPost())->update($id, $_POST);
        audit((int) $admin['id'], 'blog_updated', 'blog_post', $id);
        flash('success', 'Article mis à jour.');
        $this->redirect('/admin/blog');
    }

    public function deleteBlog(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        (new BlogPost())->delete($id);
        audit((int) $admin['id'], 'blog_deleted', 'blog_post', $id);
        flash('success', 'Article supprimé.');
        $this->redirect('/admin/blog');
    }

    public function publishBlog(int $id): void
    {
        $this->quickBlogStatus($id, 'published', 'blog_published', 'Article publié.');
    }

    public function unpublishBlog(int $id): void
    {
        $this->quickBlogStatus($id, 'draft', 'blog_unpublished', 'Article repassé en brouillon.');
    }

    public function messages(): void
    {
        Auth::requireRole('admin');
        $type = in_array(input('type'), ['contact', 'newsletter', 'rgpd'], true) ? (string) input('type') : 'all';
        $page = max(1, (int) input('page', 1));
        $result = (new ContactMessage())->paginated($type, 20, ($page - 1) * 20);
        $this->view('dashboard/messages', ['title' => 'Messages', 'messages' => $result['items'], 'type' => $type, 'pagination' => pagination_meta($result['total'], $page)]);
    }

    public function messageDetail(int $id): void
    {
        Auth::requireRole('admin');
        $message = (new ContactMessage())->find($id);
        if (!$message) {
            http_response_code(404);
            exit('Message introuvable.');
        }
        $this->view('dashboard/message-detail', ['title' => 'Message #' . $id, 'message' => $message]);
    }

    public function updateMessageStatus(int $id): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $status = (string) input('status');
        if (!in_array($status, ['read', 'processed', 'archived'], true)) {
            http_response_code(422);
            exit('Statut invalide.');
        }
        (new ContactMessage())->updateStatus($id, $status);
        $action = ['read' => 'message_read', 'processed' => 'message_processed', 'archived' => 'message_archived'][$status];
        audit((int) $admin['id'], $action, 'contact_message', $id);
        flash('success', 'Message mis à jour.');
        $this->redirect('/admin/messages');
    }

    public function readMessage(int $id): void
    {
        $this->quickMessageStatus($id, 'read', 'message_read', 'Message marqué comme lu.');
    }

    public function processMessage(int $id): void
    {
        $this->quickMessageStatus($id, 'processed', 'message_processed', 'Message marqué comme traité.');
    }

    public function archiveMessage(int $id): void
    {
        $this->quickMessageStatus($id, 'archived', 'message_archived', 'Message archivé.');
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

    private function validateProperty(): void
    {
        foreach (['title', 'type', 'short_description', 'long_description', 'city', 'region', 'capacity', 'price_per_night'] as $field) {
            if (trim((string) input($field, '')) === '') {
                flash('error', 'Les champs principaux du logement sont obligatoires.');
                $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/logements');
            }
        }
        if (!in_array(input('type'), ['treehouse', 'yurt', 'floating_cabin', 'tiny_house', 'dome', 'other'], true)) {
            http_response_code(422);
            exit('Type de logement invalide.');
        }
    }

    private function quickPropertyStatus(int $id, string $status, string $action, string $message, ?string $redirectTo = null): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        $propertyModel = new Property();
        if (!$propertyModel->find($id)) {
            http_response_code(404);
            exit('Logement introuvable.');
        }
        $propertyModel->updateStatus($id, $status);
        audit((int) $admin['id'], $action, 'property', $id);
        flash('success', $message);
        $this->redirect($redirectTo ?? '/admin/logements');
    }

    private function quickBookingStatus(int $id, string $status, string $action, string $message): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        (new Booking())->updateStatus($id, $status);
        audit((int) $admin['id'], $action, 'booking', $id);
        flash('success', $message);
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/reservations');
    }

    private function quickBlogStatus(int $id, string $status, string $action, string $message): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        (new BlogPost())->updateStatus($id, $status);
        audit((int) $admin['id'], $action, 'blog_post', $id);
        flash('success', $message);
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/blog');
    }

    private function quickMessageStatus(int $id, string $status, string $action, string $message): void
    {
        $admin = Auth::requireRole('admin');
        verify_csrf();
        (new ContactMessage())->updateStatus($id, $status);
        audit((int) $admin['id'], $action, 'contact_message', $id);
        flash('success', $message);
        $this->redirect($_SERVER['HTTP_REFERER'] ?? '/admin/messages');
    }
}
