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

    public function paginated(string $type, int $limit, int $offset): array
    {
        $where = ' WHERE 1=1';
        $params = [];
        if ($type === 'newsletter') {
            $where .= ' AND subject = "Newsletter"';
        } elseif ($type === 'rgpd') {
            $where .= ' AND subject LIKE "Demande RGPD%"';
        } elseif ($type === 'contact') {
            $where .= ' AND subject <> "Newsletter" AND subject NOT LIKE "Demande RGPD%"';
        }

        $count = $this->db->prepare('SELECT COUNT(*) FROM contact_messages' . $where);
        $count->execute($params);

        $stmt = $this->db->prepare('SELECT * FROM contact_messages' . $where . ' ORDER BY created_at DESC LIMIT ' . max(1, $limit) . ' OFFSET ' . max(0, $offset));
        $stmt->execute($params);

        return ['items' => $stmt->fetchAll(), 'total' => (int) $count->fetchColumn()];
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE contact_messages SET status = ? WHERE id = ?');
        $stmt->execute([$status, $id]);
    }
}
