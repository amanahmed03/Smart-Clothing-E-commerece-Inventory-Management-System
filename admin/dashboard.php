<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Admin\DashboardController;
$controller = new DashboardController();
$controller->dashboard();
