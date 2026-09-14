<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    public function products(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $productModel = new Product();
        $products = $productModel->getAllWithCategories();
        $data = ['pageTitle' => 'Product Management', 'adminUser' => $adminUser, 'products' => $products];
        include APP_ROOT . '/app/Views/admin/products.php';
        exit;
    }

    public function productAdd(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $errors = [];
        $categories = (new Category())->getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $stock_quantity = trim($_POST['stock_quantity'] ?? '');
            $category_id = trim($_POST['category_id'] ?? '');
            $sizes = trim($_POST['sizes'] ?? 'S,M,L,XL');
            $color = trim($_POST['color'] ?? 'Standard');
            $smart_features = trim($_POST['smart_features'] ?? '');
            if (empty($name)) { $errors[] = 'Product Name is required.'; }
            elseif (strlen($name) < 2 || strlen($name) > 150) { $errors[] = 'Product Name must be between 2 and 150 characters.'; }
            if (empty($price) || !is_numeric($price) || (float)$price <= 0) { $errors[] = 'Valid price is required.'; }
            if (empty($stock_quantity) || !is_numeric($stock_quantity) || (int)$stock_quantity < 0) { $errors[] = 'Valid stock quantity is required.'; }
            if (empty($category_id) || !is_numeric($category_id)) { $errors[] = 'Please select a category.'; }
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            $slug = trim($slug, '-');
            if (empty($slug)) { $slug = 'product-' . time(); }
            if (empty($errors)) {
                try {
                    (new Product())->create(['category_id' => (int)$category_id, 'name' => $name, 'slug' => $slug, 'description' => $description ?: null, 'price' => (float)$price, 'stock_quantity' => (int)$stock_quantity, 'sizes' => $sizes, 'color' => $color, 'smart_features' => $smart_features ?: null]);
                    set_flash('success', 'Product "' . $name . '" created successfully.');
                    $this->redirect(Auth::baseUrl() . 'admin/products.php');
                } catch (\PDOException $e) { $errors[] = 'Database error: Could not create product.'; }
            }
        }
        $data = ['pageTitle' => 'Add New Product', 'adminUser' => $adminUser, 'errors' => $errors, 'categories' => $categories, 'name' => $name ?? '', 'description' => $description ?? '', 'price' => $price ?? '', 'stock_quantity' => $stock_quantity ?? '', 'category_id' => $category_id ?? '', 'sizes' => $sizes ?? 'S,M,L,XL', 'color' => $color ?? 'Standard', 'smart_features' => $smart_features ?? '', 'slug' => $slug ?? ''];
        include APP_ROOT . '/app/Views/admin/product_add.php';
        exit;
    }

    public function productEdit(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$productId || $productId <= 0) { set_flash('error', 'Invalid product ID.'); $this->redirect(Auth::baseUrl() . 'admin/products.php'); }
        $productModel = new Product();
        $product = $productModel->findById($productId);
        if (!$product) { set_flash('error', 'Product not found.'); $this->redirect(Auth::baseUrl() . 'admin/products.php'); }
        $categories = (new Category())->getAll();
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? ''); $description = trim($_POST['description'] ?? ''); $price = trim($_POST['price'] ?? ''); $stock_quantity = trim($_POST['stock_quantity'] ?? ''); $category_id = trim($_POST['category_id'] ?? ''); $sizes = trim($_POST['sizes'] ?? 'S,M,L,XL'); $color = trim($_POST['color'] ?? 'Standard'); $smart_features = trim($_POST['smart_features'] ?? ''); $is_active = isset($_POST['is_active']) ? 1 : 0;
            if (empty($name)) { $errors[] = 'Product Name is required.'; } elseif (strlen($name) < 2 || strlen($name) > 150) { $errors[] = 'Product Name must be between 2 and 150 characters.'; }
            if (empty($price) || !is_numeric($price) || (float)$price <= 0) { $errors[] = 'Valid price is required.'; }
            if (empty($stock_quantity) || !is_numeric($stock_quantity) || (int)$stock_quantity < 0) { $errors[] = 'Valid stock quantity is required.'; }
            if (empty($category_id) || !is_numeric($category_id)) { $errors[] = 'Please select a category.'; }
            if (empty($errors)) {
                try {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))); $slug = trim($slug, '-'); if (empty($slug)) { $slug = 'product-' . time(); }
                    $productModel->update($productId, ['category_id' => (int)$category_id, 'name' => $name, 'slug' => $slug, 'description' => $description ?: null, 'price' => (float)$price, 'stock_quantity' => (int)$stock_quantity, 'sizes' => $sizes, 'color' => $color, 'smart_features' => $smart_features ?: null, 'is_active' => (int)$is_active]);
                    set_flash('success', 'Product "' . $name . '" updated successfully.');
                    $this->redirect(Auth::baseUrl() . 'admin/products.php');
                } catch (\PDOException $e) { $errors[] = 'Database error: Could not update product.'; }
            }
        }
        $data = ['pageTitle' => 'Edit Product #' . $product['id'], 'adminUser' => $adminUser, 'product' => $product, 'errors' => $errors, 'categories' => $categories, 'productId' => $productId];
        include APP_ROOT . '/app/Views/admin/product_edit.php';
        exit;
    }

    public function productDelete(): void
    {
        Auth::require_role('admin');
        $productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$productId || $productId <= 0) { set_flash('error', 'Invalid product ID.'); $this->redirect(Auth::baseUrl() . 'admin/products.php'); }
        try {
            $productModel = new Product();
            $product = $productModel->findById($productId);
            if (!$product) { set_flash('error', 'Product not found.'); } else { $productModel->delete($productId); set_flash('success', 'Product "' . $product['name'] . '" deleted successfully.'); }
        } catch (\PDOException $e) { set_flash('error', 'Cannot delete product: This product may be linked to active orders.'); }
        $this->redirect(Auth::baseUrl() . 'admin/products.php');
    }
}
