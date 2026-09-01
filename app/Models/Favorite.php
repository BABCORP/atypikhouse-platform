<?php

namespace App\Models;

use App\Core\Model;

final class Favorite extends Model
{
    public function idsForUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT property_id FROM property_favorites WHERE user_id = ?');
        $stmt->execute([$userId]);
        return array_map('intval', array_column($stmt->fetchAll(), 'property_id'));
    }

    public function exists(int $userId, int $propertyId): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM property_favorites WHERE user_id = ? AND property_id = ?');
        $stmt->execute([$userId, $propertyId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function add(int $userId, int $propertyId): void
    {
        $stmt = $this->db->prepare('INSERT IGNORE INTO property_favorites (user_id, property_id, created_at) VALUES (?, ?, NOW())');
        $stmt->execute([$userId, $propertyId]);
    }

    public function remove(int $userId, int $propertyId): void
    {
        $stmt = $this->db->prepare('DELETE FROM property_favorites WHERE user_id = ? AND property_id = ?');
        $stmt->execute([$userId, $propertyId]);
    }

    public function toggle(int $userId, int $propertyId): bool
    {
        if ($this->exists($userId, $propertyId)) {
            $this->remove($userId, $propertyId);
            return false;
        }

        $this->add($userId, $propertyId);
        return true;
    }

    public function propertiesForUser(int $userId): array
    {
        $stmt = $this->db->prepare('SELECT p.*, COALESCE(AVG(r.rating), 0) AS avg_rating, COUNT(r.id) AS reviews_count, MAX(pf.created_at) AS favorited_at,
            (SELECT image_path FROM property_images WHERE property_id = p.id ORDER BY is_main DESC, id ASC LIMIT 1) AS main_image
            FROM property_favorites pf
            JOIN properties p ON p.id = pf.property_id
            LEFT JOIN reviews r ON r.property_id = p.id AND r.status = "published"
            WHERE pf.user_id = ? AND p.status = "published"
            GROUP BY p.id
            ORDER BY favorited_at DESC');
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
}
