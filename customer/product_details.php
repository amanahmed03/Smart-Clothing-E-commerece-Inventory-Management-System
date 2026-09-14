<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Customer\ProductController;
$controller = new ProductController();
$controller->productDetails();
