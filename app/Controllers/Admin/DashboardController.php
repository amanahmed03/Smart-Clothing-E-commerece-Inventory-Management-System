<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;

class DashboardController extends Controller
{
    public function dashboard(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $userModel = new User();
        $productModel = new Product();
        $categoryModel = new Category();

        $userMetrics = $userModel->getUserCounts();
        $productMetrics = $productModel->getMetrics();
        $totalCategories = $categoryModel->getCount();
        $recentUsers = $userModel->getRecentUsers(5);

        $data = [
            'pageTitle' => 'Dashboard Overview',
            'adminUser' => $adminUser,
            'totalUsers' => (int)($userMetrics['total_users'] ?? 0),
            'totalCustomers' => (int)($userMetrics['total_customers'] ?? 0),
            'totalInventoryManagers' => (int)($userMetrics['total_inventory_managers'] ?? 0),
            'totalDeliveryManagers' => (int)($userMetrics['total_delivery_managers'] ?? 0),
            'totalProducts' => (int)($productMetrics['total_products'] ?? 0),
            'totalStock' => (int)($productMetrics['total_stock'] ?? 0),
            'totalCategories' => $totalCategories,
            'recentUsers' => $recentUsers,
        ];

        include APP_ROOT . '/app/Views/admin/dashboard.php';
        exit;
    }
}
