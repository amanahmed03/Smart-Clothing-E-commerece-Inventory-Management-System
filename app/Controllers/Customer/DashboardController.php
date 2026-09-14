<?php
namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Order;
use App\Models\User;

class DashboardController extends Controller
{
    public function dashboard(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $userModel = new User();
        $productModel = new Product();
        $cartModel = new Cart();

        $userProfile = $userModel->findById($currentUser['id']);
        $featuredProducts = $productModel->getFeatured(4);
        $cartCount = $cartModel->getTotalItems($currentUser['id']);

        $data = ['pageTitle' => 'Customer Dashboard', 'currentUser' => $currentUser, 'userProfile' => $userProfile, 'featuredProducts' => $featuredProducts, 'cartCount' => $cartCount];
        include APP_ROOT . '/app/Views/customer/dashboard.php';
        exit;
    }
}

class ProductController extends Controller
{
    public function products(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $productModel = new Product();
        $selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $searchQuery = trim($_GET['search'] ?? '');
        $categories = $productModel->getFiltered(0, '');
        $products = $productModel->getFiltered($selectedCategory, $searchQuery);
        $data = ['pageTitle' => 'Product Catalog', 'currentUser' => $currentUser, 'products' => $products, 'categories' => $categories, 'selectedCategory' => $selectedCategory, 'searchQuery' => $searchQuery];
        include APP_ROOT . '/app/Views/customer/products.php';
        exit;
    }

    public function productDetails(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$productId || $productId <= 0) { set_flash('error', 'Invalid product requested.'); $this->redirect(Auth::baseUrl() . 'customer/products.php'); }
        $productModel = new Product();
        $product = $productModel->findById($productId);
        if (!$product || (int)$product['is_active'] !== 1) { set_flash('error', 'The requested product is unavailable.'); $this->redirect(Auth::baseUrl() . 'customer/products.php'); }
        $availableSizes = array_filter(array_map('trim', explode(',', $product['sizes'] ?? 'S,M,L,XL')));
        $data = ['pageTitle' => $product['name'], 'currentUser' => $currentUser, 'product' => $product, 'availableSizes' => $availableSizes];
        include APP_ROOT . '/app/Views/customer/product_details.php';
        exit;
    }
}

class CartController extends Controller
{
    public function cart(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $cartModel = new Cart();
        $cartItems = $cartModel->getItems($currentUser['id']);
        $totalAmount = 0.00; $totalQuantity = 0;
        foreach ($cartItems as $item) { $subtotal = $item['price'] * $item['quantity']; $totalAmount += $subtotal; $totalQuantity += $item['quantity']; }
        $data = ['pageTitle' => 'My Shopping Cart', 'currentUser' => $currentUser, 'cartItems' => $cartItems, 'totalAmount' => $totalAmount, 'totalQuantity' => $totalQuantity];
        include APP_ROOT . '/app/Views/customer/cart.php';
        exit;
    }
}

class OrderController extends Controller
{
    public function orders(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $orderModel = new Order();
        $orders = $orderModel->getUserOrders($currentUser['id']);
        $data = ['pageTitle' => 'My Orders', 'currentUser' => $currentUser, 'orders' => $orders];
        include APP_ROOT . '/app/Views/customer/orders.php';
        exit;
    }

    public function orderDetail(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$orderId || $orderId <= 0) { set_flash('error', 'Invalid order ID.'); $this->redirect(Auth::baseUrl() . 'customer/orders.php'); }
        $orderModel = new Order();
        $order = $orderModel->findById($orderId, $currentUser['id']);
        if (!$order) { set_flash('error', 'Order not found.'); $this->redirect(Auth::baseUrl() . 'customer/orders.php'); }
        $orderItems = $orderModel->getOrderItems($orderId);
        $delivery = $orderModel->getDeliveryInfo($orderId);
        $payment = $orderModel->getPaymentInfo($orderId);
        $data = ['pageTitle' => 'Order #' . $order['order_number'], 'currentUser' => $currentUser, 'order' => $order, 'orderItems' => $orderItems, 'delivery' => $delivery, 'payment' => $payment];
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
        $totalAmount = 0.00; $totalQuantity = 0;
        foreach ($cartItems as $item) { $totalAmount += $item['price'] * $item['quantity']; $totalQuantity += $item['quantity']; }
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
                    $orderModel->pdo->beginTransaction();
                    $orderNumber = 'ORD-' . strtoupper(substr(uniqid(), 0, 8));
                    $orderId = $orderModel->createOrder($currentUser['id'], $orderNumber, $totalAmount, $shippingAddress, $shippingPhone, $notes);
                    $insertItemStmt = $orderModel->pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, selected_size) VALUES (:order_id, :product_id, :quantity, :price, :selected_size)");
                    foreach ($cartItems as $item) {
                        $insertItemStmt->execute([':order_id' => $orderId, ':product_id' => $item['product_id'], ':quantity' => $item['quantity'], ':price' => $item['price'], ':selected_size' => $item['selected_size']]);
                        $reduceStock = $orderModel->pdo->prepare("UPDATE products SET stock_quantity = stock_quantity - :qty WHERE id = :pid");
                        $reduceStock->execute([':qty' => $item['quantity'], ':pid' => $item['product_id']]);
                    }
                    $orderModel->createPayment($orderId, $paymentMethod, $totalAmount);
                    $orderModel->createDelivery($orderId);
                    $orderModel->clearCart($currentUser['id']);
                    $orderModel->pdo->commit();
                    set_flash('success', 'Order placed successfully! Order #: ' . $orderNumber);
                    $this->redirect(Auth::baseUrl() . 'customer/orders.php');
                } catch (\PDOException $e) { $orderModel->pdo->rollBack(); $errors[] = 'Failed to place order. Please try again.'; }
            }
        }
        $data = ['pageTitle' => 'Checkout', 'currentUser' => $currentUser, 'cartItems' => $cartItems, 'totalAmount' => $totalAmount, 'totalQuantity' => $totalQuantity, 'profile' => $profile, 'errors' => $errors];
        include APP_ROOT . '/app/Views/customer/checkout.php';
        exit;
    }
}

class ProfileController extends Controller
{
    public function profile(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $userModel = new User();
        $user = $userModel->findById($currentUser['id']);
        if (!$user) { set_flash('error', 'Unable to retrieve profile data.'); $this->redirect(Auth::baseUrl() . 'customer/dashboard.php'); }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? ''); $phone = trim($_POST['phone'] ?? ''); $address = trim($_POST['address'] ?? '');
            if (empty($name)) { $errors[] = 'Full Name cannot be empty.'; } elseif (strlen($name) < 2 || strlen($name) > 100) { $errors[] = 'Name must be between 2 and 100 characters.'; }
            if (empty($errors)) { try { $userModel->update($currentUser['id'], $name, $currentUser['role']); $_SESSION['user_name'] = $name; set_flash('success', 'Your profile details have been updated successfully.'); $this->redirect(Auth::baseUrl() . 'customer/profile.php'); } catch (\PDOException $e) { $errors[] = 'Database error: Unable to update profile.'; } }
        }
        $data = ['pageTitle' => 'My Profile', 'currentUser' => $currentUser, 'user' => $user, 'errors' => $errors];
        include APP_ROOT . '/app/Views/customer/profile.php';
        exit;
    }
}
