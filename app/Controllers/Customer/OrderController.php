<?php
namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;

class OrderController extends Controller
{
    public function orders(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $orderModel = new Order();
        $orders = $orderModel->getUserOrders($currentUser['id']);
        $data = [
            'pageTitle' => 'My Orders',
            'currentUser' => $currentUser,
            'orders' => $orders
        ];
        include APP_ROOT . '/app/Views/customer/orders.php';
        exit;
    }

    public function orderDetail(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$orderId || $orderId <= 0) {
            set_flash('error', 'Invalid order ID.');
            $this->redirect(Auth::baseUrl() . 'customer/orders.php');
        }
        $orderModel = new Order();
        $order = $orderModel->findById($orderId, $currentUser['id']);
        if (!$order) {
            set_flash('error', 'Order not found.');
            $this->redirect(Auth::baseUrl() . 'customer/orders.php');
        }
        $orderItems = $orderModel->getOrderItems($orderId);
        $delivery = $orderModel->getDeliveryInfo($orderId);
        $payment = $orderModel->getPaymentInfo($orderId);
        $data = [
            'pageTitle' => 'Order #' . $order['order_number'],
            'currentUser' => $currentUser,
            'order' => $order,
            'orderItems' => $orderItems,
            'delivery' => $delivery,
            'payment' => $payment
        ];
        include APP_ROOT . '/app/Views/customer/order_detail.php';
        exit;
    }

    public function checkout(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $cartModel = new Cart();
        $orderModel = new Order();
        $userModel = new User();

        $cartItems = $cartModel->getItems($currentUser['id']);
        $totalAmount = 0.00;
        $totalQuantity = 0;
        foreach ($cartItems as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
            $totalQuantity += $item['quantity'];
        }
        $profile = $userModel->findById($currentUser['id']);
        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $shippingAddress = trim($_POST['shipping_address'] ?? '');
            $shippingPhone = trim($_POST['shipping_phone'] ?? '');
            $paymentMethod = trim($_POST['payment_method'] ?? 'cash_on_delivery');
            $notes = trim($_POST['notes'] ?? '');
            if (empty($shippingAddress)) { $errors[] = 'Shipping address is required.'; }
            if (empty($shippingPhone)) { $errors[] = 'Shipping phone number is required.'; }
            if (empty($cartItems)) { $errors[] = 'Your cart is empty.'; }
            if (empty($errors)) {
                try {
                    $orderModel->getPdo()->beginTransaction();
                    $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), 0, 8));
                    $orderId = $orderModel->createOrder($currentUser['id'], $orderNumber, $totalAmount, $shippingAddress, $shippingPhone, $notes);
                    $insertItemStmt = $orderModel->getPdo()->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, selected_size) VALUES (:order_id, :product_id, :quantity, :price, :selected_size)");
                    foreach ($cartItems as $item) {
                        $insertItemStmt->execute([
                            ':order_id' => $orderId,
                            ':product_id' => $item['product_id'],
                            ':quantity' => $item['quantity'],
                            ':price' => $item['price'],
                            ':selected_size' => $item['selected_size']
                        ]);
                        $reduceStock = $orderModel->getPdo()->prepare("UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :pid");
                        $reduceStock->execute([
                            ':qty' => $item['quantity'],
                            ':pid' => $item['product_id']
                        ]);
                    }
                    $orderModel->createPayment($orderId, $paymentMethod, $totalAmount);
                    $orderModel->createDelivery($orderId);
                    $orderModel->clearCart($currentUser['id']);
                    $orderModel->getPdo()->commit();
                    set_flash('success', 'Order placed successfully! Order #: ' . $orderNumber);
                    $this->redirect(Auth::baseUrl() . 'customer/orders.php');
                } catch (\PDOException $e) {
                    $orderModel->getPdo()->rollBack();
                    $errors[] = 'Failed to place order. Please try again.';
                }
            }
        }
        $data = [
            'pageTitle' => 'Checkout',
            'currentUser' => $currentUser,
            'cartItems' => $cartItems,
            'totalAmount' => $totalAmount,
            'totalQuantity' => $totalQuantity,
            'profile' => $profile,
            'errors' => $errors
        ];
        include APP_ROOT . '/app/Views/customer/checkout.php';
        exit;
    }
}
