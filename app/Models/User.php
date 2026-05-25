<?php

namespace App\Models;

use App\Core\Model;

final class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([strtolower(trim($email))]);
        return $stmt->fetch() ?: null;
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (first_name, last_name, email, password_hash, phone, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, "active", NOW(), NOW())');
        $stmt->execute([
            trim($data['first_name']),
            trim($data['last_name']),
            strtolower(trim($data['email'])),
            password_hash($data['password'], PASSWORD_DEFAULT),
            trim($data['phone'] ?? ''),
            $data['role'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function createOwnerProfile(int $userId, array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO owner_profiles (user_id, company_name, address, city, postal_code, country, description, verification_status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, "pending", NOW(), NOW())');
        $stmt->execute([
            $userId,
            trim($data['company_name'] ?? ''),
            trim($data['address'] ?? ''),
            trim($data['city'] ?? ''),
            trim($data['postal_code'] ?? ''),
            trim($data['country'] ?? 'France'),
            trim($data['description'] ?? ''),
        ]);
    }

    public function all(?string $role = null, ?string $status = null): array
    {
        $sql = 'SELECT * FROM users WHERE 1=1';
        $params = [];
        if ($role) {
            $sql .= ' AND role = ?';
            $params[] = $role;
        }
        if ($status) {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }
        $sql .= ' ORDER BY created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function updateProfile(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE users SET first_name = ?, last_name = ?, phone = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([trim($data['first_name']), trim($data['last_name']), trim($data['phone'] ?? ''), $id]);
    }

    public function updatePassword(int $id, string $password): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
    }

    public function ownerProfiles(): array
    {
        $stmt = $this->db->query('SELECT op.*, u.first_name, u.last_name, u.email FROM owner_profiles op JOIN users u ON u.id = op.user_id ORDER BY op.created_at DESC');
        return $stmt->fetchAll();
    }

    public function ownerProfile(int $userId): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM owner_profiles WHERE user_id = ? LIMIT 1');
        $stmt->execute([$userId]);
        return $stmt->fetch() ?: null;
    }

    public function updateOwnerProfile(int $userId, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE owner_profiles SET company_name = ?, address = ?, city = ?, postal_code = ?, country = ?, description = ?, updated_at = NOW() WHERE user_id = ?');
        $stmt->execute([
            trim($data['company_name'] ?? ''),
            trim($data['address'] ?? ''),
            trim($data['city'] ?? ''),
            trim($data['postal_code'] ?? ''),
            trim($data['country'] ?? 'France'),
            trim($data['description'] ?? ''),
            $userId,
        ]);
    }

    public function updateOwnerProfileStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE owner_profiles SET verification_status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }
}
