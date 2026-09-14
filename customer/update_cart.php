<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Customer\CartController;
$controller = new CartController();
$controller->updateCart();
