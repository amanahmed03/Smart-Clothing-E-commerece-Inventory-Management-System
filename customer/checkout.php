<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Customer\OrderController;
$controller = new OrderController();
$controller->checkout();
