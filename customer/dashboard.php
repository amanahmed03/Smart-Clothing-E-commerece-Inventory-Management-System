<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Customer\DashboardController;
$controller = new DashboardController();
$controller->dashboard();
