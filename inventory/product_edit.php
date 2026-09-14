<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Inventory\ProductController;
$controller = new ProductController();
$controller->productEdit();
