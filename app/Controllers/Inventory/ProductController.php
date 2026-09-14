<?php
namespace App\Controllers\Inventory;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function products(): void
    {
        Auth::require_role('inventory_manager');
        $invUser = Auth::currentUser();
        $productModel = new Product();
        $selectedCategory = isset($_GET['category']) ? (int)$_GET['category'] : 0;
        $searchQuery = trim($_GET['search'] ?? '');
        $categories = $productModel->getFiltered(0, '');
        $products = $productModel->getFiltered($selectedCategory, $searchQuery);
        $data = [
            'pageTitle' => 'Inventory Products',
            'invUser' => $invUser,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'searchQuery' => $searchQuery
        ];
        include APP_ROOT . '/app/Views/inventory/products.php';
        exit;
    }

    public function productEdit(): void
    {
        Auth::require_role('inventory_manager');
        $invUser = Auth::currentUser();
        $productModel = new Product();
        $productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$productId || $productId <= 0) {
            set_flash('error', 'Invalid product ID.');
            $this->redirect(Auth::baseUrl() . 'inventory/products.php');
        }
        $product = $productModel->findById($productId);
        if (!$product) {
            set_flash('error', 'Product not found.');
            $this->redirect(Auth::baseUrl() . 'inventory/products.php');
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $stock = trim($_POST['stock_quantity'] ?? '');
            $sizes = trim($_POST['sizes'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            if (empty($name)) { $errors[] = 'Product name is required.'; }
            if (empty($price) || !is_numeric($price) || $price < 0) { $errors[] = 'Valid price is required.'; }
            if (empty($stock) || !is_numeric($stock) || $stock < 0) { $errors[] = 'Valid stock quantity is required.'; }
            if (empty($errors)) {
                try {
                    $productModel->update($productId, [
                        'name' => $name,
                        'slug' => strtolower(str_replace(' ', '_', $name)),
                        'description' => $description,
                        'price' => (float)$price,
                        'stock_quantity' => (int)$stock,
                        'sizes' => $sizes,
                        'color' => '',
                        'smart_features' => '',
                        'is_active' => $isActive
                    ]);
                    set_flash('success', 'Product updated successfully.');
                    $this->redirect(Auth::baseUrl() . 'inventory/products.php');
                } catch (\PDOException $e) {
                    $errors[] = 'Database error: Could not update product.';
                }
            }
        }
        $data = [
            'pageTitle' => 'Edit Product',
            'invUser' => $invUser,
            'product' => $product,
            'errors' => $errors
        ];
        include APP_ROOT . '/app/Views/inventory/product_edit.php';
        exit;
    }
}
