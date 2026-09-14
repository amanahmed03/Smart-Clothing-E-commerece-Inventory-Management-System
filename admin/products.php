<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Admin\ProductController;
$controller = new ProductController();
$controller->products();
