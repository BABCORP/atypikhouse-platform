<?php

namespace App\Models;

use App\Core\Model;

final class ContactMessage extends Model
{
    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO contact_messages (name, email, subject, message, status, created_at) VALUES (?, ?, ?, ?, "new", NOW())');
        $stmt->execute([
            trim($data['name']),
            strtolower(trim($data['email'])),
            trim($data['subject']),
            trim($data['message']),
        ]);
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }
}
