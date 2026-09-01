<?php

namespace App\Models;

use App\Core\Model;
use Throwable;

final class PropertyChangeRequest extends Model
{
    public function createOrReplace(int $propertyId, int $ownerId, array $proposedData): int
    {
        $json = json_encode($proposedData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        $existing = $this->pendingForProperty($propertyId);
        if ($existing) {
            $stmt = $this->db->prepare('UPDATE property_change_requests SET proposed_data = ?, rejection_reason = NULL, updated_at = NOW() WHERE id = ?');
            $stmt->execute([$json, (int) $existing['id']]);
            return (int) $existing['id'];
        }

        $stmt = $this->db->prepare('INSERT INTO property_change_requests (property_id, owner_id, status, proposed_data, created_at, updated_at) VALUES (?, ?, "pending", ?, NOW(), NOW())');
        $stmt->execute([$propertyId, $ownerId, $json]);
        return (int) $this->db->lastInsertId();
    }

    public function pendingForProperty(int $propertyId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM property_change_requests WHERE property_id = ? AND status = "pending" ORDER BY updated_at DESC LIMIT 1');
        $stmt->execute([$propertyId]);
        return $stmt->fetch() ?: null;
    }

    public function pendingCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM property_change_requests WHERE status = "pending"')->fetchColumn();
    }

    public function allPending(): array
    {
        $stmt = $this->db->query('SELECT pcr.*, p.title, p.city, p.region, u.email AS owner_email, u.first_name AS owner_first_name, u.last_name AS owner_last_name
            FROM property_change_requests pcr
            JOIN properties p ON p.id = pcr.property_id
            JOIN users u ON u.id = pcr.owner_id
            WHERE pcr.status = "pending"
            ORDER BY pcr.updated_at DESC');
        return $stmt->fetchAll();
    }

    public function findForAdmin(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT pcr.*, p.title, p.slug, p.type, p.short_description, p.long_description, p.address, p.city, p.postal_code, p.region, p.country, p.capacity, p.bedrooms, p.beds, p.bathrooms, p.price_per_night, p.cleaning_fee, p.eco_score, p.status AS property_status, u.email AS owner_email, u.first_name AS owner_first_name, u.last_name AS owner_last_name
            FROM property_change_requests pcr
            JOIN properties p ON p.id = pcr.property_id
            JOIN users u ON u.id = pcr.owner_id
            WHERE pcr.id = ?
            LIMIT 1');
        $stmt->execute([$id]);
        $request = $stmt->fetch();
        if (!$request) {
            return null;
        }
        $request['proposed'] = json_decode((string) $request['proposed_data'], true) ?: [];
        return $request;
    }

    public function latestForOwnerProperties(int $ownerId): array
    {
        $stmt = $this->db->prepare('SELECT pcr.* FROM property_change_requests pcr JOIN properties p ON p.id = pcr.property_id WHERE p.owner_id = ? ORDER BY pcr.updated_at DESC');
        $stmt->execute([$ownerId]);
        return $stmt->fetchAll();
    }

    public function approve(int $id, int $adminId): ?int
    {
        $request = $this->findForAdmin($id);
        if (!$request || $request['status'] !== 'pending') {
            return null;
        }

        try {
            $this->db->beginTransaction();
            (new Property())->applyApprovedChange((int) $request['property_id'], $request['proposed']);
            $stmt = $this->db->prepare('UPDATE property_change_requests SET status = "approved", reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW() WHERE id = ?');
            $stmt->execute([$adminId, $id]);
            $this->db->commit();
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }

        return (int) $request['property_id'];
    }

    public function reject(int $id, int $adminId, string $reason): ?int
    {
        $request = $this->findForAdmin($id);
        if (!$request || $request['status'] !== 'pending') {
            return null;
        }

        $stmt = $this->db->prepare('UPDATE property_change_requests SET status = "rejected", rejection_reason = ?, reviewed_by = ?, reviewed_at = NOW(), updated_at = NOW() WHERE id = ?');
        $stmt->execute([mb_substr(trim($reason), 0, 500), $adminId, $id]);

        return (int) $request['property_id'];
    }
}
