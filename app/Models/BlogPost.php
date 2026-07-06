<?php

namespace App\Models;

use App\Core\Model;

final class BlogPost extends Model
{
    public function published(): array
    {
        return $this->db->query('SELECT * FROM blog_posts WHERE status = "published" ORDER BY created_at DESC')->fetchAll();
    }

    public function findBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM blog_posts WHERE slug = ? AND status = "published" LIMIT 1');
        $stmt->execute([$slug]);
        return $stmt->fetch() ?: null;
    }

    public function all(): array
    {
        return $this->db->query('SELECT bp.*, u.email AS author_email FROM blog_posts bp LEFT JOIN users u ON u.id = bp.author_id ORDER BY bp.created_at DESC')->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM blog_posts WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function create(array $data, int $authorId): void
    {
        $stmt = $this->db->prepare('INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, status, author_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
        $stmt->execute([
            trim($data['title']),
            slugify($data['title']),
            trim($data['excerpt']),
            trim($data['content']),
            trim($data['featured_image'] ?? 'assets/img/properties/default-placeholder.svg'),
            $data['status'] ?? 'draft',
            $authorId,
        ]);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE blog_posts SET title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, status = ?, updated_at = NOW() WHERE id = ?');
        $stmt->execute([trim($data['title']), slugify($data['title']), trim($data['excerpt']), trim($data['content']), trim($data['featured_image'] ?? 'assets/img/properties/default-placeholder.svg'), $data['status'], $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM blog_posts WHERE id = ?');
        $stmt->execute([$id]);
    }
}
