<?php
namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Product;
use App\Models\Category;

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
        $data = [
            'pageTitle' => 'Product Catalog',
            'currentUser' => $currentUser,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'searchQuery' => $searchQuery
        ];
        include APP_ROOT . '/app/Views/customer/products.php';
        exit;
    }

    public function productDetails(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$productId || $productId <= 0) {
            set_flash('error', 'Invalid product requested.');
            $this->redirect(Auth::baseUrl() . 'customer/products.php');
        }
        $productModel = new Product();
        $product = $productModel->findById($productId);
        if (!$product || (int)$product['is_active'] !== 1) {
            set_flash('error', 'The requested product is unavailable.');
            $this->redirect(Auth::baseUrl() . 'customer/products.php');
        }
        $availableSizes = array_filter(array_map('trim', explode(',', $product['sizes'] ?? 'S,M,L,XL')));
        $data = [
            'pageTitle' => $product['name'],
            'currentUser' => $currentUser,
            'product' => $product,
            'availableSizes' => $availableSizes
        ];
        include APP_ROOT . '/app/Views/customer/product_details.php';
        exit;
    }
}
