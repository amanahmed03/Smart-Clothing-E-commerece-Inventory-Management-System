<?php
namespace App\Controllers\Inventory;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Category;

class DashboardController extends Controller
{
    public function dashboard(): void
    {
        Auth::require_role('inventory_manager');
        $invUser = Auth::currentUser();
        $productModel = new Product();
        $totalProducts = (int)$productModel->getMetrics()['total_products'];
        $lowStock = (int)$productModel->pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity < 5")->fetchColumn();
        $outOfStock = (int)$productModel->pdo->query("SELECT COUNT(*) FROM products WHERE stock_quantity = 0")->fetchColumn();
        $totalStockValue = (float)$productModel->getStockValue();
        $lowStockItems = $productModel->getLowStockItems();
        $recentProductsStmt = $productModel->pdo->query("SELECT p.id, p.name, c.name AS category_name, p.price, p.stock_quantity, p.is_active FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT 10");
        $recentProducts = $recentProductsStmt->fetchAll();
        $data = ['pageTitle' => 'Inventory Dashboard', 'invUser' => $invUser, 'totalProducts' => $totalProducts, 'lowStock' => $lowStock, 'outOfStock' => $outOfStock, 'totalStockValue' => $totalStockValue, 'lowStockItems' => $lowStockItems, 'recentProducts' => $recentProducts];
        include APP_ROOT . '/app/Views/inventory/dashboard.php';
        exit;
    }
}

class ProductController extends Controller
{
    public function products(): void
    {
        Auth::require_role('inventory_manager');
        $invUser = Auth::currentUser();
        $productModel = new Product();
        $products = $productModel->getAllWithCategories();
        $totalStockValue = 0; $totalItems = 0;
        foreach ($products as $p) { $totalStockValue += $p['price'] * (int)$p['stock_quantity']; $totalItems += (int)$p['stock_quantity']; }
        $data = ['pageTitle' => 'Product Stock Management', 'invUser' => $invUser, 'products' => $products, 'totalStockValue' => $totalStockValue, 'totalItems' => $totalItems];
        include APP_ROOT . '/app/Views/inventory/products.php';
        exit;
    }

    public function productEdit(): void
    {
        Auth::require_role('inventory_manager');
        $invUser = Auth::currentUser();
        $productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$productId || $productId <= 0) { set_flash('error', 'Invalid product ID.'); $this->redirect(Auth::baseUrl() . 'inventory/products.php'); }
        $productModel = new Product();
        $product = $productModel->findById($productId);
        if (!$product) { set_flash('error', 'Product not found.'); $this->redirect(Auth::baseUrl() . 'inventory/products.php'); }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $stock_quantity = trim($_POST['stock_quantity'] ?? ''); $is_active = isset($_POST['is_active']) ? 1 : 0;
            if (!is_numeric($stock_quantity) || (int)$stock_quantity < 0) { $errors[] = 'Valid stock quantity is required.'; }
            if (empty($errors)) { try { $productModel->updateStock($productId, (int)$stock_quantity, (int)$is_active); set_flash('success', 'Stock for "' . $product['name'] . '" updated to ' . (int)$stock_quantity . ' units.'); $this->redirect(Auth::baseUrl() . 'inventory/products.php'); } catch (\PDOException $e) { $errors[] = 'Database error: Could not update stock.'; } }
        }
        $data = ['pageTitle' => 'Edit Stock: ' . $product['name'], 'invUser' => $invUser, 'product' => $product, 'errors' => $errors];
        include APP_ROOT . '/app/Views/inventory/product_edit.php';
        exit;
    }
}
