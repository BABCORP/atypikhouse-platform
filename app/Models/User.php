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

    public function emailExistsForOther(string $email, int $userId): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM users WHERE email = ? AND id <> ?');
        $stmt->execute([strtolower(trim($email)), $userId]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (first_name, last_name, email, password_hash, phone, role, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, "pending", NOW(), NOW())');
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

    public function paginated(?string $role, ?string $status, int $limit, int $offset, ?string $search = null): array
    {
        $where = ' WHERE 1=1';
        $params = [];
        if ($search && trim($search) !== '') {
            $where .= ' AND (first_name LIKE ? OR last_name LIKE ? OR email LIKE ?)';
            $term = '%' . trim($search) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        if ($role) {
            $where .= ' AND role = ?';
            $params[] = $role;
        }
        if ($status) {
            $where .= ' AND status = ?';
            $params[] = $status;
        }

        $count = $this->db->prepare('SELECT COUNT(*) FROM users' . $where);
        $count->execute($params);

        $stmt = $this->db->prepare('SELECT * FROM users' . $where . ' ORDER BY created_at DESC LIMIT ' . max(1, $limit) . ' OFFSET ' . max(0, $offset));
        $stmt->execute($params);

        return ['items' => $stmt->fetchAll(), 'total' => (int) $count->fetchColumn()];
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE users SET status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([$status, $id]);
    }

    public function updateAdminUser(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([
            trim($data['first_name']),
            trim($data['last_name']),
            strtolower(trim($data['email'])),
            trim($data['phone'] ?? ''),
            $data['role'],
            $data['status'],
            $id,
        ]);
    }

    public function activeAdminCount(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users WHERE role = "admin" AND status = "active"')->fetchColumn();
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

    public function createPasswordReset(int $userId, string $tokenHash): void
    {
        $this->db->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$userId]);
        $stmt = $this->db->prepare('INSERT INTO password_resets (user_id, token, expires_at, created_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR), NOW())');
        $stmt->execute([$userId, $tokenHash]);
    }

    public function findPasswordReset(string $tokenHash): ?array
    {
        $stmt = $this->db->prepare('SELECT pr.*, u.email FROM password_resets pr JOIN users u ON u.id = pr.user_id WHERE pr.token = ? AND pr.expires_at >= NOW() LIMIT 1');
        $stmt->execute([$tokenHash]);
        return $stmt->fetch() ?: null;
    }

    public function deletePasswordResets(int $userId): void
    {
        $this->db->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$userId]);
    }

    public function ownerProfiles(?string $status = null, ?string $search = null): array
    {
        $sql = 'SELECT op.*, u.first_name, u.last_name, u.email, u.status AS user_status,
            COUNT(p.id) AS properties_count,
            SUM(CASE WHEN p.status = "published" THEN 1 ELSE 0 END) AS published_properties,
            SUM(CASE WHEN p.status = "pending" THEN 1 ELSE 0 END) AS pending_properties
            FROM owner_profiles op
            JOIN users u ON u.id = op.user_id
            LEFT JOIN properties p ON p.owner_id = u.id
            WHERE 1=1';
        $params = [];
        if ($status) {
            $sql .= ' AND op.verification_status = ?';
            $params[] = $status;
        }
        if ($search && trim($search) !== '') {
            $sql .= ' AND (u.email LIKE ? OR u.first_name LIKE ? OR u.last_name LIKE ? OR op.company_name LIKE ?)';
            $term = '%' . trim($search) . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }
        $sql .= ' GROUP BY op.id, u.id ORDER BY op.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
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
