<?php
namespace App\Models;

use App\Core\Database;

class Delivery
{
    protected \PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function getDashboardStats(): array
    {
        $totalOrders = (int)$this->pdo->query("SELECT COUNT(*) AS total FROM orders")->fetchColumn();
        $pendingAssignment = (int)$this->pdo->query("SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status = 'pending_assignment'")->fetchColumn();
        $outForDelivery = (int)$this->pdo->query("SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status = 'out_for_delivery'")->fetchColumn();
        $deliveredToday = (int)$this->pdo->query("SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status = 'delivered' AND DATE(delivered_at) = CURDATE()")->fetchColumn();

        return compact('totalOrders', 'pendingAssignment', 'outForDelivery', 'deliveredToday');
    }

    public function getRecentDeliveries(int $limit = 10): array
    {
        $stmt = $this->pdo->prepare("
            SELECT d.id, d.order_id, d.delivery_status, d.tracking_number, d.estimated_delivery, d.delivered_at,
                   o.order_number, o.total_amount, u.name AS customer_name
            FROM deliveries d
            JOIN orders o ON d.order_id = o.id
            JOIN users u ON o.user_id = u.id
            ORDER BY d.created_at DESC
            LIMIT :limit
        ");
        $stmt->execute([':limit' => $limit]);
        return $stmt->fetchAll();
    }

    public function getOrdersWithDelivery(): array
    {
        $stmt = $this->pdo->query("
            SELECT o.id AS order_id, o.order_number, o.total_amount, o.status, o.shipping_address, o.shipping_phone,
                   u.name AS customer_name, u.email AS customer_email,
                   d.delivery_status, d.delivery_manager_id, d.tracking_number, d.estimated_delivery, d.delivered_at,
                   d.id AS delivery_id
            FROM orders o
            JOIN users u ON o.user_id = u.id
            LEFT JOIN deliveries d ON o.id = d.order_id
            ORDER BY o.created_at DESC
        ");
        return $stmt->fetchAll();
    }

    public function findOrder(int $id): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM orders WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function getOrderItems(int $orderId): array
    {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, p.name AS product_name, p.image_url
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }

    public function getDeliveryManagers(): array
    {
        $stmt = $this->pdo->query("SELECT id, name FROM users WHERE role = 'delivery_manager' ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    public function assignDelivery(int $orderId, int $managerId, ?string $trackingNumber, ?string $estimatedDelivery): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE deliveries 
            SET delivery_manager_id = :manager_id, tracking_number = :tracking, 
                estimated_delivery = :est_delivery, delivery_status = 'assigned',
                updated_at = CURRENT_TIMESTAMP
            WHERE order_id = :order_id
        ");
        return $stmt->execute([
            ':manager_id' => $managerId, ':tracking' => $trackingNumber ?: null, ':est_delivery' => $estimatedDelivery ?: null, ':order_id' => $orderId
        ]);
    }

    public function createDeliveryRecord(int $orderId, int $managerId, ?string $trackingNumber, ?string $estimatedDelivery): bool
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO deliveries (order_id, delivery_manager_id, tracking_number, estimated_delivery, delivery_status)
            VALUES (:order_id, :manager_id, :tracking, :est_delivery, 'assigned')
        ");
        return $stmt->execute([
            ':order_id' => $orderId, ':manager_id' => $managerId, ':tracking' => $trackingNumber ?: null, ':est_delivery' => $estimatedDelivery ?: null
        ]);
    }

    public function updateDeliveryStatus(int $orderId, string $status): bool
    {
        $stmt = $this->pdo->prepare("
            UPDATE deliveries SET delivery_status = :status, updated_at = CURRENT_TIMESTAMP,
            delivered_at = CASE WHEN :status = 'delivered' THEN NOW() ELSE delivered_at END
            WHERE order_id = :order_id
        ");
        return $stmt->execute([':status' => $status, ':order_id' => $orderId]);
    }
}
