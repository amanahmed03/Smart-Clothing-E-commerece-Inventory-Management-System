<?php
namespace App\Models;

use App\Core\Database;

class Cart
{
    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getPdo(): \PDO
    {
        return $this->pdo;
    }

    public function getItems(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT ci.id AS cart_item_id, ci.quantity, ci.selected_size,
                   p.id AS product_id, p.name AS product_name, p.price, p.stock_quantity,
                   c.name AS category_name
            FROM cart ct
            JOIN cart_items ci ON ct.id = ci.cart_id
            JOIN products p ON ci.product_id = p.id
            JOIN categories c ON p.category_id = c.id
            WHERE ct.user_id = :user_id
            ORDER BY ci.id DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getTotalItems(int $userId): int
    {
        $stmt = $this->pdo->prepare("
            SELECT SUM(ci.quantity) AS total_count
            FROM cart c
            JOIN cart_items ci ON c.id = ci.cart_id
            WHERE c.user_id = :user_id
        ");
        $stmt->execute([':user_id' => $userId]);
        $row = $stmt->fetch();
        return (int)($row['total_count'] ?? 0);
    }

    public function getOrCreateCartId(int $userId): int
    {
        $stmt = $this->pdo->prepare("SELECT id FROM cart WHERE user_id = :user_id LIMIT 1");
        $stmt->execute([':user_id' => $userId]);
        $cart = $stmt->fetch();

        if ($cart) {
            return (int)$cart['id'];
        }

        $createStmt = $this->pdo->prepare("INSERT INTO cart (user_id) VALUES (:user_id)");
        $createStmt->execute([':user_id' => $userId]);
        return (int)$this->pdo->lastInsertId();
    }

    public function addItem(int $cartId, int $productId, int $quantity, string $selectedSize): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO cart_items (cart_id, product_id, quantity, selected_size)
            VALUES (:cart_id, :product_id, :quantity, :selected_size)
            ON DUPLICATE KEY UPDATE quantity = quantity + :quantity
        ");
        return $stmt->execute([
            ':cart_id' => $cartId, ':product_id' => $productId, ':quantity' => $quantity, ':selected_size' => $selectedSize
        ]);
    }

    public function updateItemQuantity(int $cartItemId, int $quantity): bool
    {
        $stmt = $this->pdo->prepare("UPDATE cart_items SET quantity = :quantity WHERE id = :id");
        return $stmt->execute([':quantity' => $quantity, ':id' => $cartItemId]);
    }

    public function removeItem(int $cartItemId): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM cart_items WHERE id = :id');
        return $stmt->execute([':id' => $cartItemId]);
    }

    public function clearCart(int $userId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE cart_id = (SELECT id FROM cart WHERE user_id = :user_id)");
        $stmt->execute([':user_id' => $userId]);
        $stmt2 = $this->pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
        return $stmt2->execute([':user_id' => $userId]);
    }

    public function getItemById(int $cartItemId, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT ci.id, ci.quantity, p.id AS product_id, p.name AS product_name, p.stock_quantity
            FROM cart_items ci
            JOIN cart c ON ci.cart_id = c.id
            JOIN products p ON ci.product_id = p.id
            WHERE ci.id = :cart_item_id AND c.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([':cart_item_id' => $cartItemId, ':user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }
}
