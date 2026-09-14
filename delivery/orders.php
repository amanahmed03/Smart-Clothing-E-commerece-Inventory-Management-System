<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Delivery\OrderController;
$controller = new OrderController();
$controller->orders();
