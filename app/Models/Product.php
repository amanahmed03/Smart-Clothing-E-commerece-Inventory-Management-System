<?php
namespace App\Models;

use App\Core\Database;

class Product
{
    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getAllWithCategories(): array
    {
        $stmt = $this->pdo->query("
            SELECT p.id, p.name, p.slug, p.price, p.stock_quantity, p.is_active,
                   c.name AS category_name, p.created_at
            FROM products p
            JOIN categories c ON p.category_id = c.id
            ORDER BY p.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getActiveWithCategories(): array
    {
        $stmt = $this->pdo->query("
            SELECT p.id, p.category_id, p.name, p.slug, p.description, p.price, p.stock_quantity,
                   p.sizes, p.color, p.smart_features, p.image_url, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            ORDER BY p.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function getFeatured(int $limit = 4): array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.name, p.price, p.stock_quantity, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.is_active = 1
            ORDER BY p.id ASC
            LIMIT :limit
        ");
        $stmt->execute([':limit' => $limit]);
        return $stmt->fetchAll();
    }

    public function getFiltered(int $categoryId = 0, string $search = ''): array
    {
        $sql = "SELECT p.id, p.category_id, p.name, p.slug, p.description, p.price, p.stock_quantity,
                       p.sizes, p.color, p.smart_features, p.image_url, c.name AS category_name
                FROM products p
                JOIN categories c ON p.category_id = c.id
                WHERE p.is_active = 1";
        $params = [];

        if ($categoryId > 0) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $categoryId;
        }
        if (!empty($search)) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search OR p.smart_features LIKE :search)";
            $params[':search'] = '%' . $search . '%';
        }
        $sql .= " ORDER BY p.id ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO products (category_id, name, slug, description, price, stock_quantity, sizes, color, smart_features, image_url, is_active)
            VALUES (:category_id, :name, :slug, :description, :price, :stock_quantity, :sizes, :color, :smart_features, :image_url, 1)
        ");
        return $stmt->execute([
            ':category_id' => (int)$data['category_id'],
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?: null,
            ':price' => (float)$data['price'],
            ':stock_quantity' => (int)$data['stock_quantity'],
            ':sizes' => $data['sizes'],
            ':color' => $data['color'],
            ':smart_features' => $data['smart_features'] ?: null,
            ':image_url' => 'assets/images/products/' . strtolower(str_replace(' ', '_', $data['slug'])) . '.jpg'
        ]);
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE products 
            SET category_id = :category_id, name = :name, slug = :slug, description = :description,
                price = :price, stock_quantity = :stock_quantity, sizes = :sizes, color = :color,
                smart_features = :smart_features, is_active = :is_active, updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ");
        return $stmt->execute([
            ':category_id' => (int)$data['category_id'],
            ':name' => $data['name'],
            ':slug' => $data['slug'],
            ':description' => $data['description'] ?: null,
            ':price' => (float)$data['price'],
            ':stock_quantity' => (int)$data['stock_quantity'],
            ':sizes' => $data['sizes'],
            ':color' => $data['color'],
            ':smart_features' => $data['smart_features'] ?: null,
            ':is_active' => (int)$data['is_active'],
            ':id' => $id
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }

    public function updateStock(int $id, int $quantity, int $isActive): bool
    {
        $stmt = $this->pdo->prepare('UPDATE products SET stock_quantity = :stock_quantity, is_active = :is_active, updated_at = CURRENT_TIMESTAMP WHERE id = :id');
        return $stmt->execute([':stock_quantity' => $quantity, ':is_active' => $isActive, ':id' => $id]);
    }

    public function getMetrics(): array
    {
        $stmt = $this->pdo->query("
            SELECT COUNT(*) AS total_products, COALESCE(SUM(stock_quantity), 0) AS total_stock
            FROM products
        ");
        return $stmt->fetch();
    }

    public function getLowStockItems(int $threshold = 5): array
    {
        $stmt = $this->pdo->prepare("
            SELECT p.id, p.name, p.stock_quantity, c.name AS category_name
            FROM products p
            JOIN categories c ON p.category_id = c.id
            WHERE p.stock_quantity < :threshold AND p.is_active = 1
            ORDER BY p.stock_quantity ASC
            LIMIT 10
        ");
        $stmt->execute([':threshold' => $threshold]);
        return $stmt->fetchAll();
    }

    public function getStockValue(): float
    {
        $stmt = $this->pdo->query("SELECT COALESCE(SUM(price * stock_quantity), 0) AS total FROM products");
        return (float)$stmt->fetchColumn();
    }
}
