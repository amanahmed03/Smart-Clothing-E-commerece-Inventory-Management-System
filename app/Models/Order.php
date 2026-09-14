<?php
namespace App\Models;

use App\Core\Database;

class Order
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

    public function createOrder(int $userId, string $orderNumber, float $totalAmount, string $shippingAddress, string $shippingPhone, ?string $notes): int
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO orders (order_number, user_id, total_amount, status, shipping_address, shipping_phone, notes)
            VALUES (:order_number, :user_id, :total_amount, 'pending', :shipping_address, :shipping_phone, :notes)
        ");
        $stmt->execute([
            ':order_number' => $orderNumber, ':user_id' => $userId, ':total_amount' => $totalAmount,
            ':shipping_address' => $shippingAddress, ':shipping_phone' => $shippingPhone, ':notes' => $notes ?: null
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function addOrderItem(int $orderId, int $productId, int $quantity, float $price, string $selectedSize): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO order_items (order_id, product_id, quantity, price, selected_size)
            VALUES (:order_id, :product_id, :quantity, :price, :selected_size)
        ");
        return $stmt->execute([
            ':order_id' => $orderId, ':product_id' => $productId, ':quantity' => $quantity, ':price' => $price, ':selected_size' => $selectedSize
        ]);
    }

    public function createPayment(int $orderId, string $paymentMethod, float $amount): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO payments (order_id, payment_method, payment_status, amount)
            VALUES (:order_id, :payment_method, 'completed', :amount)
        ");
        return $stmt->execute([':order_id' => $orderId, ':payment_method' => $paymentMethod, ':amount' => $amount]);
    }

    public function createDelivery(int $orderId): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO deliveries (order_id, delivery_status) VALUES (:order_id, 'pending_assignment')");
        return $stmt->execute([':order_id' => $orderId]);
    }

    public function reduceStock(int $productId, int $qty): bool
    {
        $stmt = $this->pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :pid");
        return $stmt->execute([':qty' => $qty, ':pid' => $productId]);
    }

    public function clearCart(int $userId): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM cart_items WHERE cart_id = (SELECT id FROM cart WHERE user_id = :user_id)");
        $stmt->execute([':user_id' => $userId]);
        $stmt2 = $this->pdo->prepare("DELETE FROM cart WHERE user_id = :user_id");
        return $stmt2->execute([':user_id' => $userId]);
    }

    public function getUserOrders(int $userId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT o.id AS order_id, o.order_number, o.total_amount, o.status, o.created_at, o.updated_at,
                   d.delivery_status, d.tracking_number
            FROM orders o
            LEFT JOIN deliveries d ON o.id = d.order_id
            WHERE o.user_id = :user_id
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id, int $userId): ?array
    {
        $stmt = $this->pdo->prepare("
            SELECT o.*, u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone, u.address AS customer_address
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.id = :id AND o.user_id = :user_id
            LIMIT 1
        ");
        $stmt->execute([':id' => $id, ':user_id' => $userId]);
        return $stmt->fetch() ?: null;
    }

    public function getOrderItems(int $orderId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, p.name AS product_name
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function getDeliveryInfo(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM deliveries WHERE order_id = :id LIMIT 1");
        $stmt->execute([':id' => $orderId]);
        return $stmt->fetch() ?: null;
    }

    public function getPaymentInfo(int $orderId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM payments WHERE order_id = :id LIMIT 1");
        $stmt->execute([':id' => $orderId]);
        return $stmt->fetch() ?: null;
    }

    public function getAllOrders(): array
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS total FROM orders");
        return ['total' => (int)$stmt->fetchColumn()];
    }
}
