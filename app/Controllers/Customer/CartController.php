<?php
namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function addToCart(): void
    {
        Auth::require_role('customer');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect(Auth::baseUrl() . 'customer/products.php'); }
        $productId = filter_var($_POST['product_id'] ?? null, FILTER_VALIDATE_INT);
        $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_VALIDATE_INT);
        $selectedSize = trim($_POST['selected_size'] ?? 'M');
        if (!$productId || $productId <= 0) { set_flash('error', 'Invalid product selection.'); $this->redirect(Auth::baseUrl() . 'customer/products.php'); }
        if (!$quantity || $quantity <= 0) { $quantity = 1; }
        $currentUser = Auth::currentUser();
        $productModel = new Product();
        $cartModel = new Cart();
        try {
            $product = $productModel->findById($productId);
            if (!$product || (int)$product['is_active'] !== 1) { set_flash('error', 'The requested product is not available.'); $this->redirect(Auth::baseUrl() . 'customer/products.php'); }
            $availableStock = (int)$product['stock_quantity'];
            if ($availableStock <= 0) { set_flash('error', 'Sorry, "' . $product['name'] . '" is currently out of stock.'); $this->redirect(Auth::baseUrl() . 'customer/product_details.php?id=' . $productId); }
            $cartId = $cartModel->getOrCreateCartId($currentUser['id']);
            $itemStmt = $cartModel->getPdo()->prepare("SELECT id, quantity FROM cart_items WHERE cart_id = :cart_id AND product_id = :product_id AND selected_size = :size LIMIT 1");
            $itemStmt->execute([':cart_id' => $cartId, ':product_id' => $productId, ':size' => $selectedSize]);
            $existingItem = $itemStmt->fetch();
            if ($existingItem) {
                $newQuantity = (int)$existingItem['quantity'] + $quantity;
                if ($newQuantity > $availableStock) { $newQuantity = $availableStock; set_flash('error', 'Cart quantity adjusted to maximum available stock (' . $availableStock . ') for ' . $product['name'] . '.'); } else { set_flash('success', 'Updated quantity for "' . $product['name'] . '" in your cart.'); }
                $updateStmt = $cartModel->getPdo()->prepare("UPDATE cart_items SET quantity = :quantity WHERE id = :id");
                $updateStmt->execute([':quantity' => $newQuantity, ':id' => $existingItem['id']]);
            } else {
                if ($quantity > $availableStock) { $quantity = $availableStock; set_flash('error', 'Cart quantity adjusted to maximum available stock (' . $availableStock . ').'); } else { set_flash('success', 'Added "' . $product['name'] . '" to your shopping cart.'); }
                $insertItemStmt = $cartModel->getPdo()->prepare("INSERT INTO cart_items (cart_id, product_id, quantity, selected_size) VALUES (:cart_id, :product_id, :quantity, :selected_size)");
                $insertItemStmt->execute([':cart_id' => $cartId, ':product_id' => $productId, ':quantity' => $quantity, ':selected_size' => $selectedSize]);
            }
            $this->redirect(Auth::baseUrl() . 'customer/cart.php');
        } catch (\PDOException $e) { set_flash('error', 'Failed to update shopping cart.'); $this->redirect(Auth::baseUrl() . 'customer/products.php'); }
    }

    public function updateCart(): void
    {
        Auth::require_role('customer');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirect(Auth::baseUrl() . 'customer/cart.php'); }
        $cartItemId = filter_var($_POST['cart_item_id'] ?? null, FILTER_VALIDATE_INT);
        $quantity = filter_var($_POST['quantity'] ?? null, FILTER_VALIDATE_INT);
        $currentUser = Auth::currentUser();
        $cartModel = new Cart();
        if (!$cartItemId || $cartItemId <= 0) { set_flash('error', 'Invalid cart item selected.'); $this->redirect(Auth::baseUrl() . 'customer/cart.php'); }
        try {
            $item = $cartModel->getItemById($cartItemId, $currentUser['id']);
            if (!$item) { set_flash('error', 'Unauthorized access or item not found.'); $this->redirect(Auth::baseUrl() . 'customer/cart.php'); }
            if ($quantity <= 0) { $cartModel->removeItem($cartItemId); set_flash('success', 'Removed "' . $item['product_name'] . '" from your cart.'); $this->redirect(Auth::baseUrl() . 'customer/cart.php'); }
            $availableStock = (int)$item['stock_quantity'];
            if ($quantity > $availableStock) { $quantity = $availableStock; set_flash('error', 'Quantity limited to available stock (' . $availableStock . ') for "' . $item['product_name'] . '".'); } else { set_flash('success', 'Updated quantity for "' . $item['product_name'] . '".'); }
            $cartModel->updateItemQuantity($cartItemId, $quantity);
            $this->redirect(Auth::baseUrl() . 'customer/cart.php');
        } catch (\PDOException $e) { set_flash('error', 'Failed to update item quantity.'); $this->redirect(Auth::baseUrl() . 'customer/cart.php'); }
    }

    public function cart(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $cartModel = new Cart();
        $items = $cartModel->getItems($currentUser['id']);
        $totalItems = $cartModel->getTotalItems($currentUser['id']);
        $totalAmount = 0;
        foreach ($items as $item) {
            $totalAmount += (float)$item['price'] * (int)$item['quantity'];
        }
        $this->setData([
            'cartItems' => $items,
            'totalQuantity' => $totalItems,
            'totalAmount' => $totalAmount
        ]);
        $this->render('customer/cart');
    }

    public function removeFromCart(): void
    {
        Auth::require_role('customer');
        $cartItemId = filter_var($_POST['cart_item_id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
        $currentUser = Auth::currentUser();
        $cartModel = new Cart();
        if (!$cartItemId || $cartItemId <= 0) { set_flash('error', 'Invalid item specified for removal.'); $this->redirect(Auth::baseUrl() . 'customer/cart.php'); }
        try {
            $item = $cartModel->getItemById($cartItemId, $currentUser['id']);
            if (!$item) { set_flash('error', 'Unauthorized access or item not found.'); $this->redirect(Auth::baseUrl() . 'customer/cart.php'); }
            $cartModel->removeItem($cartItemId); set_flash('success', 'Removed "' . $item['product_name'] . '" from your shopping cart.');
            $this->redirect(Auth::baseUrl() . 'customer/cart.php');
        } catch (\PDOException $e) { set_flash('error', 'Failed to remove item from cart.'); $this->redirect(Auth::baseUrl() . 'customer/cart.php'); }
    }
}
