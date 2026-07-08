<?php

namespace App\Models;

use App\Core\Model;

final class Review extends Model
{
    public function forProperty(int $propertyId): array
    {
        $stmt = $this->db->prepare('SELECT r.*, u.first_name FROM reviews r JOIN users u ON u.id = r.tenant_id WHERE r.property_id = ? AND r.status = "published" ORDER BY r.created_at DESC');
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    public function forTenant(int $tenantId): array
    {
        $stmt = $this->db->prepare('SELECT r.*, p.title FROM reviews r JOIN properties p ON p.id = r.property_id WHERE r.tenant_id = ? ORDER BY r.created_at DESC');
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll();
    }

    public function canReview(int $bookingId, int $tenantId): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM bookings b LEFT JOIN reviews r ON r.booking_id = b.id WHERE b.id = ? AND b.tenant_id = ? AND b.status = "completed" AND r.id IS NULL');
        $stmt->execute([$bookingId, $tenantId]);
        return (int) $stmt->fetchColumn() === 1;
    }

    public function create(array $booking, int $rating, string $comment): void
    {
        $stmt = $this->db->prepare('INSERT INTO reviews (booking_id, property_id, tenant_id, rating, comment, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, "pending", NOW(), NOW())');
        $stmt->execute([$booking['id'], $booking['property_id'], $booking['tenant_id'], $rating, trim($comment)]);
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT r.*, p.title, u.email FROM reviews r JOIN properties p ON p.id = r.property_id JOIN users u ON u.id = r.tenant_id ORDER BY r.created_at DESC');
        return $stmt->fetchAll();
    }

    public function filtered(array $filters = []): array
    {
        $sql = 'SELECT r.*, p.title, u.email FROM reviews r JOIN properties p ON p.id = r.property_id JOIN users u ON u.id = r.tenant_id WHERE 1=1';
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= ' AND r.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['rating'])) {
            $sql .= ' AND r.rating = ?';
            $params[] = (int) $filters['rating'];
        }
        if (!empty($filters['property'])) {
            $sql .= ' AND p.title LIKE ?';
            $params[] = '%' . trim((string) $filters['property']) . '%';
        }
        if (!empty($filters['author'])) {
            $sql .= ' AND u.email LIKE ?';
            $params[] = '%' . trim((string) $filters['author']) . '%';
        }
        $sql .= ' ORDER BY r.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE reviews SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM reviews WHERE id = ?');
        $stmt->execute([$id]);
    }
}
