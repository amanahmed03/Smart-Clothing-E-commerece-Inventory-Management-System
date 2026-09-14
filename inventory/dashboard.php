<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Inventory\DashboardController;
$controller = new DashboardController();
$controller->dashboard();
