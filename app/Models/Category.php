<?php
namespace App\Models;

use App\Core\Database;

class Category
{
    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getAll(): array
    {
        $stmt = $this->pdo->query("
            SELECT c.id, c.name, c.slug, c.description, c.created_at,
                   COUNT(p.id) AS product_count
            FROM categories c
            LEFT JOIN products p ON c.id = p.category_id
            GROUP BY c.id, c.name, c.slug, c.description, c.created_at
            ORDER BY c.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM categories WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function create(string $name, string $description): bool
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $slug = trim($slug, '-');
        if (empty($slug)) {
            $slug = 'cat-' . time();
        }
        $stmt = $this->pdo->prepare('INSERT INTO categories (name, slug, description) VALUES (:name, :slug, :description)');
        return $stmt->execute([':name' => $name, ':slug' => $slug, ':description' => $description ?: null]);
    }

    public function update(int $id, string $name, string $description): bool
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        $slug = trim($slug, '-');
        if (empty($slug)) {
            $slug = 'cat-' . time();
        }
        $stmt = $this->pdo->prepare('UPDATE categories SET name = :name, slug = :slug, description = :description, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([':name' => $name, ':slug' => $slug, ':description' => $description ?: null, ':id' => $id]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM categories WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function getCount(): int
    {
        $stmt = $this->pdo->query('SELECT COUNT(*) AS total FROM categories');
        return (int)$stmt->fetchColumn();
    }
}
