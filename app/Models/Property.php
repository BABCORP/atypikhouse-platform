<?php

namespace App\Models;

use App\Core\Model;

final class Property extends Model
{
    public function published(array $filters = [], int $limit = 50): array
    {
        $sql = 'SELECT p.*, COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS reviews_count,
                (SELECT image_path FROM property_images WHERE property_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1) AS main_image
                FROM properties p
                LEFT JOIN reviews r ON r.property_id = p.id AND r.status = "published"
                WHERE p.status = "published"';
        $params = [];

        if (!empty($filters['location'])) {
            $sql .= ' AND (p.city LIKE ? OR p.region LIKE ?)';
            $params[] = '%' . $filters['location'] . '%';
            $params[] = '%' . $filters['location'] . '%';
        }
        if (!empty($filters['type'])) {
            $sql .= ' AND p.type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['capacity'])) {
            $sql .= ' AND p.capacity >= ?';
            $params[] = (int) $filters['capacity'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= ' AND p.price_per_night <= ?';
            $params[] = (float) $filters['max_price'];
        }
        if (!empty($filters['amenity'])) {
            $sql .= ' AND EXISTS (SELECT 1 FROM property_amenities pa WHERE pa.property_id = p.id AND pa.amenity_name LIKE ?)';
            $params[] = '%' . $filters['amenity'] . '%';
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date']) && valid_date($filters['start_date']) && valid_date($filters['end_date'])) {
            $sql .= ' AND NOT EXISTS (
                SELECT 1 FROM bookings b
                WHERE b.property_id = p.id
                  AND b.status IN ("confirmed", "completed")
                  AND b.start_date < ? AND b.end_date > ?
            ) AND NOT EXISTS (
                SELECT 1 FROM property_availabilities pa
                WHERE pa.property_id = p.id AND pa.date >= ? AND pa.date < ? AND pa.is_available = 0
            )';
            $params[] = $filters['end_date'];
            $params[] = $filters['start_date'];
            $params[] = $filters['start_date'];
            $params[] = $filters['end_date'];
        }

        $sql .= ' GROUP BY p.id';
        $sort = $filters['sort'] ?? 'newest';
        $sql .= match ($sort) {
            'price_asc' => ' ORDER BY p.price_per_night ASC',
            'price_desc' => ' ORDER BY p.price_per_night DESC',
            default => ' ORDER BY p.created_at DESC',
        };
        $sql .= ' LIMIT ' . max(1, $limit);

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function featured(): array
    {
        return $this->published([], 6);
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, u.first_name, u.last_name, op.company_name, op.city AS owner_city
            FROM properties p
            JOIN users u ON u.id = p.owner_id
            LEFT JOIN owner_profiles op ON op.user_id = u.id
            WHERE p.slug = ? AND p.status = "published" LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function findOwned(int $id, int $ownerId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM properties WHERE id = ? AND owner_id = ? LIMIT 1');
        $stmt->execute([$id, $ownerId]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM properties WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function findForAdmin(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT p.*, u.first_name AS owner_first_name, u.last_name AS owner_last_name, u.email AS owner_email, op.company_name,
            (SELECT image_path FROM property_images WHERE property_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1) AS main_image
            FROM properties p
            JOIN users u ON u.id = p.owner_id
            LEFT JOIN owner_profiles op ON op.user_id = u.id
            WHERE p.id = ?
            LIMIT 1');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function images(int $propertyId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM property_images WHERE property_id = ? ORDER BY is_main DESC, id ASC');
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    public function amenities(int $propertyId): array
    {
        $stmt = $this->db->prepare('SELECT amenity_name FROM property_amenities WHERE property_id = ? ORDER BY amenity_name');
        $stmt->execute([$propertyId]);
        return array_column($stmt->fetchAll(), 'amenity_name');
    }

    public function ownerProperties(int $ownerId): array
    {
        $stmt = $this->db->prepare('SELECT p.*, (SELECT image_path FROM property_images WHERE property_id = p.id ORDER BY is_main DESC LIMIT 1) AS main_image FROM properties p WHERE owner_id = ? ORDER BY created_at DESC');
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function imageForOwnedProperty(int $propertyId, int $imageId, int $ownerId): ?array
    {
        $stmt = $this->db->prepare('SELECT pi.* FROM property_images pi JOIN properties p ON p.id = pi.property_id WHERE pi.id = ? AND pi.property_id = ? AND p.owner_id = ? LIMIT 1');
        $stmt->execute([$imageId, $propertyId, $ownerId]);
        return $stmt->fetch() ?: null;
    }

    public function create(int $ownerId, array $data, ?string $uploadedImage = null): int
    {
        $slug = slugify($data['title']);
        $stmt = $this->db->prepare('INSERT INTO properties (owner_id, title, slug, type, short_description, long_description, address, city, postal_code, region, country, latitude, longitude, capacity, bedrooms, beds, bathrooms, price_per_night, cleaning_fee, eco_score, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NULL, NULL, ?, ?, ?, ?, ?, ?, ?, "draft", NOW(), NOW())');
        $stmt->execute([
            $ownerId,
            trim($data['title']),
            $slug,
            $data['type'],
            trim($data['short_description']),
            trim($data['long_description']),
            trim($data['address'] ?? ''),
            trim($data['city']),
            trim($data['postal_code'] ?? ''),
            trim($data['region']),
            trim($data['country'] ?? 'France'),
            (int) $data['capacity'],
            (int) ($data['bedrooms'] ?? 1),
            (int) ($data['beds'] ?? 1),
            (int) ($data['bathrooms'] ?? 1),
            (float) $data['price_per_night'],
            (float) ($data['cleaning_fee'] ?? 0),
            (int) ($data['eco_score'] ?? 3),
        ]);
        $id = (int) $this->db->lastInsertId();
        $this->replaceAmenities($id, $data['amenities'] ?? '');
        $this->addImage($id, $uploadedImage ?: 'assets/img/properties/default-placeholder.svg', trim((string) ($data['image_alt'] ?? '')) ?: ('Photo illustrative de ' . $data['title']));
        return $id;
    }

    public function update(int $id, int $ownerId, array $data, ?string $uploadedImage = null): void
    {
        $stmt = $this->db->prepare('UPDATE properties SET title = ?, slug = ?, type = ?, short_description = ?, long_description = ?, address = ?, city = ?, postal_code = ?, region = ?, country = ?, capacity = ?, bedrooms = ?, beds = ?, bathrooms = ?, price_per_night = ?, cleaning_fee = ?, eco_score = ?, updated_at = NOW() WHERE id = ? AND owner_id = ? AND status IN ("draft", "rejected", "pending")');
        $stmt->execute([
            trim($data['title']),
            slugify($data['title']),
            $data['type'],
            trim($data['short_description']),
            trim($data['long_description']),
            trim($data['address'] ?? ''),
            trim($data['city']),
            trim($data['postal_code'] ?? ''),
            trim($data['region']),
            trim($data['country'] ?? 'France'),
            (int) $data['capacity'],
            (int) ($data['bedrooms'] ?? 1),
            (int) ($data['beds'] ?? 1),
            (int) ($data['bathrooms'] ?? 1),
            (float) $data['price_per_night'],
            (float) ($data['cleaning_fee'] ?? 0),
            (int) ($data['eco_score'] ?? 3),
            $id,
            $ownerId,
        ]);
        $this->replaceAmenities($id, $data['amenities'] ?? '');
        if ($uploadedImage) {
            $this->db->prepare('UPDATE property_images SET is_main = 0 WHERE property_id = ?')->execute([$id]);
            $this->addImage($id, $uploadedImage, trim((string) ($data['image_alt'] ?? '')) ?: ('Photo de ' . $data['title']));
        }
    }

    public function addGalleryImage(int $propertyId, int $ownerId, string $path, string $altText): bool
    {
        if (!$this->findOwned($propertyId, $ownerId)) {
            return false;
        }
        $this->db->prepare('UPDATE property_images SET is_main = 0 WHERE property_id = ?')->execute([$propertyId]);
        $this->addImage($propertyId, $path, $altText);
        return true;
    }

    public function updateImageAlt(int $propertyId, int $imageId, int $ownerId, string $altText): bool
    {
        if (!$this->imageForOwnedProperty($propertyId, $imageId, $ownerId)) {
            return false;
        }
        $stmt = $this->db->prepare('UPDATE property_images SET alt_text = ? WHERE id = ? AND property_id = ?');
        $stmt->execute([trim($altText), $imageId, $propertyId]);
        return true;
    }

    public function setMainImage(int $propertyId, int $imageId, int $ownerId): bool
    {
        if (!$this->imageForOwnedProperty($propertyId, $imageId, $ownerId)) {
            return false;
        }
        $this->db->prepare('UPDATE property_images SET is_main = 0 WHERE property_id = ?')->execute([$propertyId]);
        $this->db->prepare('UPDATE property_images SET is_main = 1 WHERE id = ? AND property_id = ?')->execute([$imageId, $propertyId]);
        return true;
    }

    public function deleteImage(int $propertyId, int $imageId, int $ownerId): bool
    {
        $image = $this->imageForOwnedProperty($propertyId, $imageId, $ownerId);
        if (!$image) {
            return false;
        }
        $this->db->prepare('DELETE FROM property_images WHERE id = ? AND property_id = ?')->execute([$imageId, $propertyId]);
        $this->ensureImage($propertyId);
        if ((int) $image['is_main'] === 1) {
            $this->db->prepare('UPDATE property_images SET is_main = 1 WHERE property_id = ? ORDER BY id ASC LIMIT 1')->execute([$propertyId]);
        }
        return true;
    }

    public function submit(int $id, int $ownerId): void
    {
        $stmt = $this->db->prepare('UPDATE properties SET status = "pending", updated_at = NOW() WHERE id = ? AND owner_id = ? AND status IN ("draft", "rejected")');
        $stmt->execute([$id, $ownerId]);
    }

    public function deleteDraft(int $id, int $ownerId): void
    {
        $stmt = $this->db->prepare('DELETE FROM properties WHERE id = ? AND owner_id = ? AND status IN ("draft", "rejected")');
        $stmt->execute([$id, $ownerId]);
    }

    public function allForAdmin(): array
    {
        $stmt = $this->db->query('SELECT p.*, u.email AS owner_email FROM properties p JOIN users u ON u.id = p.owner_id ORDER BY p.created_at DESC');
        return $stmt->fetchAll();
    }

    public function paginatedForAdmin(array $filters, int $limit, int $offset): array
    {
        $from = ' FROM properties p JOIN users u ON u.id = p.owner_id WHERE 1=1';
        $params = [];
        if (!empty($filters['status'])) {
            $from .= ' AND p.status = ?';
            $params[] = $filters['status'];
        }
        if (!empty($filters['type'])) {
            $from .= ' AND p.type = ?';
            $params[] = $filters['type'];
        }
        if (!empty($filters['owner'])) {
            $from .= ' AND (u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ?)';
            $term = '%' . trim((string) $filters['owner']) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        if (!empty($filters['location'])) {
            $from .= ' AND (p.city LIKE ? OR p.region LIKE ?)';
            $term = '%' . trim((string) $filters['location']) . '%';
            $params[] = $term;
            $params[] = $term;
        }
        if (!empty($filters['search'])) {
            $from .= ' AND p.title LIKE ?';
            $params[] = '%' . trim((string) $filters['search']) . '%';
        }

        $count = $this->db->prepare('SELECT COUNT(*)' . $from);
        $count->execute($params);

        $stmt = $this->db->prepare('SELECT p.*, u.email AS owner_email,
            (SELECT image_path FROM property_images WHERE property_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1) AS main_image' . $from . ' ORDER BY p.created_at DESC LIMIT ' . max(1, $limit) . ' OFFSET ' . max(0, $offset));
        $stmt->execute($params);

        return ['items' => $stmt->fetchAll(), 'total' => (int) $count->fetchColumn()];
    }

    public function updateAdmin(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE properties SET title = ?, type = ?, short_description = ?, long_description = ?, city = ?, postal_code = ?, region = ?, country = ?, capacity = ?, bedrooms = ?, beds = ?, bathrooms = ?, price_per_night = ?, cleaning_fee = ?, eco_score = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([
            trim($data['title']),
            $data['type'],
            trim($data['short_description']),
            trim($data['long_description']),
            trim($data['city']),
            trim($data['postal_code'] ?? ''),
            trim($data['region']),
            trim($data['country'] ?? 'France'),
            (int) $data['capacity'],
            (int) ($data['bedrooms'] ?? 1),
            (int) ($data['beds'] ?? 1),
            (int) ($data['bathrooms'] ?? 1),
            (float) $data['price_per_night'],
            (float) ($data['cleaning_fee'] ?? 0),
            (int) ($data['eco_score'] ?? 3),
            $id,
        ]);
        $this->replaceAmenities($id, $data['amenities'] ?? '');
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE properties SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function bookingCount(int $propertyId): int
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM bookings WHERE property_id = ?');
        $stmt->execute([$propertyId]);
        return (int) $stmt->fetchColumn();
    }

    public function setAvailability(int $propertyId, string $date, bool $isAvailable, ?float $priceOverride): void
    {
        $stmt = $this->db->prepare('INSERT INTO property_availabilities (property_id, date, is_available, price_override, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW()) ON DUPLICATE KEY UPDATE is_available = VALUES(is_available), price_override = VALUES(price_override), updated_at = NOW()');
        $stmt->execute([$propertyId, $date, $isAvailable ? 1 : 0, $priceOverride]);
    }

    public function setAvailabilityRange(int $propertyId, string $start, string $end, bool $isAvailable, ?float $priceOverride): int
    {
        $current = new \DateTimeImmutable($start);
        $last = new \DateTimeImmutable($end);
        $count = 0;
        while ($current <= $last) {
            $this->setAvailability($propertyId, $current->format('Y-m-d'), $isAvailable, $priceOverride);
            $current = $current->modify('+1 day');
            $count++;
        }
        return $count;
    }

    public function availabilities(int $propertyId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM property_availabilities WHERE property_id = ? AND date >= CURDATE() ORDER BY date ASC LIMIT 45');
        $stmt->execute([$propertyId]);
        return $stmt->fetchAll();
    }

    private function ensureImage(int $propertyId): void
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM property_images WHERE property_id = ?');
        $stmt->execute([$propertyId]);
        if ((int) $stmt->fetchColumn() === 0) {
            $this->addImage($propertyId, 'assets/img/properties/default-placeholder.svg', 'Image temporaire du logement');
        }
    }

    private function replaceAmenities(int $propertyId, string|array $amenities): void
    {
        $this->db->prepare('DELETE FROM property_amenities WHERE property_id = ?')->execute([$propertyId]);
        $items = is_array($amenities) ? $amenities : explode(',', $amenities);
        $stmt = $this->db->prepare('INSERT INTO property_amenities (property_id, amenity_name) VALUES (?, ?)');
        foreach ($items as $item) {
            $name = trim((string) $item);
            if ($name !== '') {
                $stmt->execute([$propertyId, $name]);
            }
        }
    }

    private function addImage(int $propertyId, string $path, string $altText): void
    {
        $stmt = $this->db->prepare('INSERT INTO property_images (property_id, image_path, alt_text, is_main, created_at) VALUES (?, ?, ?, 1, NOW())');
        $stmt->execute([$propertyId, $path, trim($altText)]);
    }
}
