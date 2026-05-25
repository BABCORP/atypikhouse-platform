<?php

namespace App\Models;

use App\Core\Model;

final class Booking extends Model
{
    public function isAvailable(int $propertyId, string $start, string $end): bool
    {
        if ($start < date('Y-m-d') || nights_between($start, $end) < 1) {
            return false;
        }

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM bookings WHERE property_id = ? AND status IN ("confirmed", "completed") AND start_date < ? AND end_date > ?');
        $stmt->execute([$propertyId, $end, $start]);
        if ((int) $stmt->fetchColumn() > 0) {
            return false;
        }

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM property_availabilities WHERE property_id = ? AND date >= ? AND date < ? AND is_available = 0');
        $stmt->execute([$propertyId, $start, $end]);
        return (int) $stmt->fetchColumn() === 0;
    }

    public function create(array $property, int $tenantId, array $data): int
    {
        $nights = nights_between($data['start_date'], $data['end_date']);
        $subtotal = $nights * (float) $property['price_per_night'];
        $cleaning = (float) $property['cleaning_fee'];
        $total = $subtotal + $cleaning;

        $stmt = $this->db->prepare('INSERT INTO bookings (property_id, tenant_id, start_date, end_date, nights, guests_count, subtotal, cleaning_fee, total_price, status, payment_status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "pending_payment", "not_paid", NOW(), NOW())');
        $stmt->execute([
            $property['id'],
            $tenantId,
            $data['start_date'],
            $data['end_date'],
            $nights,
            (int) $data['guests_count'],
            $subtotal,
            $cleaning,
            $total,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function findForTenant(int $id, int $tenantId): ?array
    {
        $stmt = $this->db->prepare('SELECT b.*, p.title, p.slug, p.city FROM bookings b JOIN properties p ON p.id = b.property_id WHERE b.id = ? AND b.tenant_id = ?');
        $stmt->execute([$id, $tenantId]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT b.*, p.title, p.owner_id, p.slug, u.email AS tenant_email FROM bookings b JOIN properties p ON p.id = b.property_id JOIN users u ON u.id = b.tenant_id WHERE b.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function tenantBookings(int $tenantId): array
    {
        $stmt = $this->db->prepare('SELECT b.*, p.title, p.slug, p.city FROM bookings b JOIN properties p ON p.id = b.property_id WHERE b.tenant_id = ? ORDER BY b.start_date DESC');
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll();
    }

    public function ownerBookings(int $ownerId): array
    {
        $stmt = $this->db->prepare('SELECT b.*, p.title, p.slug, u.email AS tenant_email FROM bookings b JOIN properties p ON p.id = b.property_id JOIN users u ON u.id = b.tenant_id WHERE p.owner_id = ? ORDER BY b.start_date DESC');
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT b.*, p.title, u.email AS tenant_email FROM bookings b JOIN properties p ON p.id = b.property_id JOIN users u ON u.id = b.tenant_id ORDER BY b.created_at DESC');
        return $stmt->fetchAll();
    }

    public function simulatePayment(int $bookingId, bool $success): bool
    {
        $booking = $this->find($bookingId);
        if (!$booking || $booking['status'] !== 'pending_payment') {
            return false;
        }
        if ($success) {
            $stmt = $this->db->prepare('SELECT COUNT(*) FROM bookings WHERE id <> ? AND property_id = ? AND status IN ("confirmed", "completed") AND start_date < ? AND end_date > ?');
            $stmt->execute([$bookingId, $booking['property_id'], $booking['end_date'], $booking['start_date']]);
            $success = (int) $stmt->fetchColumn() === 0;
        }
        $status = $success ? 'test_success' : 'test_failed';
        $transaction = 'TEST-' . strtoupper(bin2hex(random_bytes(5)));
        $this->db->prepare('INSERT INTO payments (booking_id, provider, test_transaction_id, amount, status, created_at) VALUES (?, "simulation", ?, ?, ?, NOW())')
            ->execute([$bookingId, $transaction, $booking['total_price'], $status]);

        if ($success) {
            $this->db->prepare('UPDATE bookings SET status = "confirmed", payment_status = "test_paid", updated_at = NOW() WHERE id = ?')->execute([$bookingId]);
        } else {
            $this->db->prepare('UPDATE bookings SET status = "cancelled", updated_at = NOW() WHERE id = ?')->execute([$bookingId]);
        }
        return $success;
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }
}
