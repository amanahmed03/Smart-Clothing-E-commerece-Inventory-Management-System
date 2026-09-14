<?php
namespace App\Controllers\Delivery;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Delivery;
use App\Models\Order;

class DashboardController extends Controller
{
    public function dashboard(): void
    {
        Auth::require_role('delivery_manager');
        $delUser = Auth::currentUser();
        $deliveryModel = new Delivery();
        $stats = $deliveryModel->getDashboardStats();
        $recentDeliveries = $deliveryModel->getRecentDeliveries();
        $data = ['pageTitle' => 'Delivery Dashboard', 'delUser' => $delUser, 'stats' => $stats, 'recentDeliveries' => $recentDeliveries];
        include APP_ROOT . '/app/Views/delivery/dashboard.php';
        exit;
    }
}

class OrderController extends Controller
{
    public function orders(): void
    {
        Auth::require_role('delivery_manager');
        $delUser = Auth::currentUser();
        $deliveryModel = new Delivery();
        $filterStatus = isset($_GET['status']) ? $_GET['status'] : '';
        $orders = $deliveryModel->getOrdersWithDelivery();
        if ($filterStatus) {
            $orders = array_filter($orders, fn($o) => $o['status'] === $filterStatus);
        }
        $deliveryManagers = $deliveryModel->getDeliveryManagers();
        $data = ['pageTitle' => 'Delivery Orders', 'delUser' => $delUser, 'orders' => array_values($orders), 'deliveryManagers' => $deliveryManagers, 'filterStatus' => $filterStatus];
        include APP_ROOT . '/app/Views/delivery/orders.php';
        exit;
    }

    public function orderDetail(): void
    {
        Auth::require_role('delivery_manager');
        $delUser = Auth::currentUser();
        $orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$orderId || $orderId <= 0) {
            set_flash('error', 'Invalid order ID.');
            $this->redirect(Auth::baseUrl() . 'delivery/orders.php');
        }
        $deliveryModel = new Delivery();
        $order = $deliveryModel->findOrder($orderId);
        if (!$order) {
            set_flash('error', 'Order not found.');
            $this->redirect(Auth::baseUrl() . 'delivery/orders.php');
        }
        $orderItems = $deliveryModel->getOrderItems($orderId);
        $delivery = $deliveryModel->pdo->prepare("SELECT * FROM deliveries WHERE order_id = :id LIMIT 1");
        $delivery->execute([':id' => $orderId]);
        $delivery = $delivery->fetch();
        $deliveryManagers = $deliveryModel->getDeliveryManagers();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_delivery'])) {
            $managerId = $_POST['delivery_manager_id'] ?? '';
            $trackingNumber = trim($_POST['tracking_number'] ?? '');
            $estimatedDelivery = trim($_POST['estimated_delivery'] ?? '');
            if (empty($managerId)) {
                $errors[] = 'Please select a delivery manager.';
            }
            if (empty($errors)) {
                try {
                    if ($delivery) {
                        $updStmt = $deliveryModel->pdo->prepare("UPDATE deliveries SET delivery_manager_id = :manager_id, tracking_number = :tracking, estimated_delivery = :est_delivery, delivery_status = 'assigned', updated_at = CURRENT_TIMESTAMP WHERE order_id = :order_id");
                        $updStmt->execute([
                            ':manager_id' => (int)$managerId,
                            ':tracking' => $trackingNumber ?: null,
                            ':est_delivery' => $estimatedDelivery ?: null,
                            ':order_id' => $orderId
                        ]);
                    } else {
                        $insStmt = $deliveryModel->pdo->prepare("INSERT INTO deliveries (order_id, delivery_manager_id, tracking_number, estimated_delivery, delivery_status) VALUES (:order_id, :manager_id, :tracking, :est_delivery, 'assigned')");
                        $insStmt->execute([
                            ':order_id' => $orderId,
                            ':manager_id' => (int)$managerId,
                            ':tracking' => $trackingNumber ?: null,
                            ':est_delivery' => $estimatedDelivery ?: null
                        ]);
                    }
                    set_flash('success', 'Delivery assigned successfully for Order #' . $order['order_number']);
                    $this->redirect(Auth::baseUrl() . 'delivery/order_detail.php?id=' . $orderId);
                } catch (\PDOException $e) {
                    $errors[] = 'Database error: Could not assign delivery.';
                }
            }
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
            $newStatus = trim($_POST['delivery_status'] ?? '');
            $validStatuses = ['pending_assignment', 'assigned', 'out_for_delivery', 'delivered', 'returned'];
            if (!in_array($newStatus, $validStatuses)) {
                $errors[] = 'Invalid delivery status.';
            } else {
                try {
                    $deliveryModel->updateDeliveryStatus($orderId, $newStatus);
                    $orderStatusMap = [
                        'pending_assignment' => 'pending',
                        'assigned' => 'processing',
                        'out_for_delivery' => 'shipped',
                        'delivered' => 'delivered',
                        'returned' => 'cancelled'
                    ];
                    if (isset($orderStatusMap[$newStatus])) {
                        $ordUpd = $deliveryModel->pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
                        $ordUpd->execute([
                            ':status' => $orderStatusMap[$newStatus],
                            ':id' => $orderId
                        ]);
                    }
                    set_flash('success', 'Delivery status updated to "' . $newStatus . '".');
                    $this->redirect(Auth::baseUrl() . 'delivery/order_detail.php?id=' . $orderId);
                } catch (\PDOException $e) {
                    $errors[] = 'Database error: Could not update status.';
                }
            }
        }
        $data = [
            'pageTitle' => 'Order #' . $order['order_number'],
            'delUser' => $delUser,
            'order' => $order,
            'orderItems' => $orderItems,
            'delivery' => $delivery,
            'deliveryManagers' => $deliveryManagers,
            'errors' => $errors
        ];
        include APP_ROOT . '/app/Views/delivery/order_detail.php';
        exit;
    }
}
