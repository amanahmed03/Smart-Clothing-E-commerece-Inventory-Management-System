<?php
namespace App\Controllers\Delivery;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Order;
use App\Models\Delivery;

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
