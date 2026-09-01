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

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM bookings WHERE property_id = ? AND status IN ("pending_admin", "pending_payment", "confirmed", "completed") AND start_date < ? AND end_date > ?');
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
        $pricing = $this->calculatePrice((int) $property['id'], (string) $data['start_date'], (string) $data['end_date'], (float) $property['price_per_night'], (float) $property['cleaning_fee']);
        if ($pricing['nights'] < 1) {
            throw new \InvalidArgumentException('La date de départ doit être postérieure à la date d’arrivée.');
        }

        $stmt = $this->db->prepare('INSERT INTO bookings (property_id, tenant_id, start_date, end_date, nights, guests_count, subtotal, cleaning_fee, total_price, status, payment_status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, "pending_payment", "not_paid", NOW(), NOW())');
        $stmt->execute([
            $property['id'],
            $tenantId,
            $data['start_date'],
            $data['end_date'],
            $pricing['nights'],
            (int) $data['guests_count'],
            $pricing['subtotal'],
            $pricing['cleaning_fee'],
            $pricing['total'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function calculatePrice(int $propertyId, string $start, string $end, float $basePrice, float $cleaningFee): array
    {
        if (!valid_date($start) || !valid_date($end) || nights_between($start, $end) < 1) {
            return [
                'nights' => 0,
                'nights_prices' => [],
                'price_per_night' => max(0, $basePrice),
                'cleaning_fee' => max(0, $cleaningFee),
                'subtotal' => 0.0,
                'total' => 0.0,
            ];
        }

        $prices = $this->nightlyPrices($propertyId, $start, $end, $basePrice);
        $subtotal = array_sum(array_column($prices, 'price'));

        return [
            'nights' => count($prices),
            'nights_prices' => $prices,
            'price_per_night' => $basePrice,
            'subtotal' => $subtotal,
            'cleaning_fee' => $cleaningFee,
            'total' => $subtotal + $cleaningFee,
        ];
    }

    public function nightlyPrices(int $propertyId, string $start, string $end, float $basePrice): array
    {
        if (!valid_date($start) || !valid_date($end) || nights_between($start, $end) < 1) {
            return [];
        }

        $stmt = $this->db->prepare('SELECT date, price_override FROM property_availabilities WHERE property_id = ? AND date >= ? AND date < ? AND price_override IS NOT NULL');
        $stmt->execute([$propertyId, $start, $end]);
        $overrides = [];
        foreach ($stmt->fetchAll() as $row) {
            $overrides[$row['date']] = (float) $row['price_override'];
        }

        $prices = [];
        $current = new \DateTimeImmutable($start);
        $last = new \DateTimeImmutable($end);
        while ($current < $last) {
            $date = $current->format('Y-m-d');
            $prices[] = [
                'date' => $date,
                'price' => $overrides[$date] ?? $basePrice,
                'is_override' => array_key_exists($date, $overrides),
            ];
            $current = $current->modify('+1 day');
        }
        return $prices;
    }

    public function findForTenant(int $id, int $tenantId): ?array
    {
        $stmt = $this->db->prepare('SELECT b.*, p.title, p.slug, p.city, u.first_name AS tenant_first_name, u.last_name AS tenant_last_name, u.email AS tenant_email, pay.test_transaction_id,
                r.id AS review_id,
                r.status AS review_status,
                CASE
                    WHEN b.status IN ("confirmed", "completed")
                        AND b.payment_status = "test_paid"
                        AND b.end_date <= CURDATE()
                        AND r.id IS NULL
                    THEN 1 ELSE 0
                END AS can_review
            FROM bookings b
            JOIN properties p ON p.id = b.property_id
            JOIN users u ON u.id = b.tenant_id
            LEFT JOIN reviews r ON r.booking_id = b.id
            LEFT JOIN payments pay ON pay.booking_id = b.id
            WHERE b.id = ? AND b.tenant_id = ?
            ORDER BY pay.created_at DESC
            LIMIT 1');
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
        $stmt = $this->db->prepare('SELECT b.*, p.title, p.slug, p.city,
                r.id AS review_id,
                r.status AS review_status,
                CASE
                    WHEN b.status IN ("confirmed", "completed")
                        AND b.payment_status = "test_paid"
                        AND b.end_date <= CURDATE()
                        AND r.id IS NULL
                    THEN 1 ELSE 0
                END AS can_review
            FROM bookings b
            JOIN properties p ON p.id = b.property_id
            LEFT JOIN reviews r ON r.booking_id = b.id
            WHERE b.tenant_id = ?
            ORDER BY b.start_date DESC');
        $stmt->execute([$tenantId]);
        return $stmt->fetchAll();
    }

    public function ownerBookings(int $ownerId, array $filters = []): array
    {
        $sql = 'SELECT b.*, p.title, p.slug, u.email AS tenant_email FROM bookings b JOIN properties p ON p.id = b.property_id JOIN users u ON u.id = b.tenant_id WHERE p.owner_id = ?';
        $params = [$ownerId];
        if (!empty($filters['property_id'])) {
            $sql .= ' AND p.id = ?';
            $params[] = (int) $filters['property_id'];
        }
        if (!empty($filters['status'])) {
            $sql .= ' AND b.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['start_date']) && valid_date($filters['start_date'])) {
            $sql .= ' AND b.start_date >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date']) && valid_date($filters['end_date'])) {
            $sql .= ' AND b.end_date <= ?';
            $params[] = $filters['end_date'];
        }
        $sql .= ' ORDER BY b.start_date DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function bookedDatesForOwnerProperty(int $propertyId, int $ownerId): array
    {
        $stmt = $this->db->prepare('SELECT b.start_date, b.end_date FROM bookings b JOIN properties p ON p.id = b.property_id WHERE b.property_id = ? AND p.owner_id = ? AND b.status IN ("pending_admin", "pending_payment", "confirmed", "completed") AND b.end_date >= CURDATE() ORDER BY b.start_date ASC');
        $stmt->execute([$propertyId, $ownerId]);
        $dates = [];
        foreach ($stmt->fetchAll() as $booking) {
            $current = new \DateTimeImmutable($booking['start_date']);
            $end = new \DateTimeImmutable($booking['end_date']);
            while ($current < $end) {
                $dates[] = $current->format('Y-m-d');
                $current = $current->modify('+1 day');
            }
        }
        return array_values(array_unique($dates));
    }

    public function bookedDatesForProperty(int $propertyId): array
    {
        $stmt = $this->db->prepare('SELECT start_date, end_date FROM bookings WHERE property_id = ? AND status IN ("pending_admin", "pending_payment", "confirmed", "completed") AND end_date >= CURDATE() ORDER BY start_date ASC');
        $stmt->execute([$propertyId]);
        $dates = [];
        foreach ($stmt->fetchAll() as $booking) {
            $current = new \DateTimeImmutable($booking['start_date']);
            $end = new \DateTimeImmutable($booking['end_date']);
            while ($current < $end) {
                $dates[] = $current->format('Y-m-d');
                $current = $current->modify('+1 day');
            }
        }
        return array_values(array_unique($dates));
    }

    public function all(array $filters = []): array
    {
        $sql = 'SELECT b.*, p.title, p.slug, u.email AS tenant_email FROM bookings b JOIN properties p ON p.id = b.property_id JOIN users u ON u.id = b.tenant_id WHERE 1=1';
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= ' AND b.status = ?';
            $params[] = $filters['status'];
        }
        $sql .= ' ORDER BY b.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function paginatedAll(array $filters, int $limit, int $offset): array
    {
        $sql = ' FROM bookings b JOIN properties p ON p.id = b.property_id JOIN users u ON u.id = b.tenant_id JOIN users owner ON owner.id = p.owner_id WHERE 1=1';
        $params = [];
        if (!empty($filters['status'])) {
            $sql .= ' AND b.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['payment_status'])) {
            $sql .= ' AND b.payment_status = ?';
            $params[] = $filters['payment_status'];
        }
        if (!empty($filters['property'])) {
            $sql .= ' AND p.title LIKE ?';
            $params[] = '%' . trim((string) $filters['property']) . '%';
        }
        if (!empty($filters['tenant'])) {
            $sql .= ' AND (u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)';
            $term = '%' . trim((string) $filters['tenant']) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        if (!empty($filters['owner'])) {
            $sql .= ' AND (owner.email LIKE ? OR owner.first_name LIKE ? OR owner.last_name LIKE ?)';
            $term = '%' . trim((string) $filters['owner']) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        if (!empty($filters['start_date']) && valid_date($filters['start_date'])) {
            $sql .= ' AND b.start_date >= ?';
            $params[] = $filters['start_date'];
        }
        if (!empty($filters['end_date']) && valid_date($filters['end_date'])) {
            $sql .= ' AND b.end_date <= ?';
            $params[] = $filters['end_date'];
        }

        $count = $this->db->prepare('SELECT COUNT(*)' . $sql);
        $count->execute($params);

        $stmt = $this->db->prepare('SELECT b.*, p.title, p.slug, u.email AS tenant_email, owner.email AS owner_email' . $sql . ' ORDER BY b.created_at DESC LIMIT ' . max(1, $limit) . ' OFFSET ' . max(0, $offset));
        $stmt->execute($params);

        return ['items' => $stmt->fetchAll(), 'total' => (int) $count->fetchColumn()];
    }

    public function findForAdmin(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT b.*, p.title, p.slug, p.city, p.owner_id, p.price_per_night, owner.email AS owner_email, owner.first_name AS owner_first_name, owner.last_name AS owner_last_name, u.first_name AS tenant_first_name, u.last_name AS tenant_last_name, u.email AS tenant_email, pay.test_transaction_id, pay.status AS payment_provider_status, pay.created_at AS payment_created_at
            FROM bookings b
            JOIN properties p ON p.id = b.property_id
            JOIN users u ON u.id = b.tenant_id
            JOIN users owner ON owner.id = p.owner_id
            LEFT JOIN payments pay ON pay.booking_id = b.id
            WHERE b.id = ?
            ORDER BY pay.created_at DESC
            LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function simulatePayment(int $bookingId, bool $success): bool
    {
        $booking = $this->find($bookingId);
        if (!$booking || $booking['status'] !== 'pending_payment' || $booking['payment_status'] === 'test_paid') {
            return false;
        }
        if ((float) $booking['total_price'] <= 0 || in_array($booking['status'], ['cancelled', 'completed'], true)) {
            return false;
        }

        $status = $success ? 'test_success' : 'test_failed';
        $transaction = 'AH-PAY-' . date('Ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
        $this->db->prepare('INSERT INTO payments (booking_id, provider, test_transaction_id, amount, status, created_at) VALUES (?, "simulation", ?, ?, ?, NOW())')
            ->execute([$bookingId, $transaction, $booking['total_price'], $status]);

        if ($success) {
            $this->db->prepare('UPDATE bookings SET status = "confirmed", payment_status = "test_paid", updated_at = NOW() WHERE id = ?')->execute([$bookingId]);
        } else {
            $this->db->prepare('UPDATE bookings SET payment_status = "test_failed", updated_at = NOW() WHERE id = ?')->execute([$bookingId]);
        }
        return $success;
    }

    public function confirmStripeTestPayment(int $bookingId, string $sessionId): bool
    {
        $booking = $this->find($bookingId);
        if (!$booking) {
            return false;
        }
        if ($booking['payment_status'] === 'test_paid') {
            return true;
        }
        if ($booking['status'] !== 'pending_payment' || (float) $booking['total_price'] <= 0) {
            return false;
        }

        $this->db->prepare('INSERT INTO payments (booking_id, provider, test_transaction_id, amount, status, created_at) VALUES (?, "stripe_test", ?, ?, "test_success", NOW())')
            ->execute([$bookingId, $sessionId, $booking['total_price']]);
        $this->db->prepare('UPDATE bookings SET status = "confirmed", payment_status = "test_paid", updated_at = NOW() WHERE id = ?')->execute([$bookingId]);

        return true;
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE bookings SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function validateForPayment(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE bookings SET status = "pending_payment", payment_status = "not_paid", updated_at = NOW() WHERE id = ? AND status = "pending_admin"');
        $stmt->execute([$id]);
    }
}
