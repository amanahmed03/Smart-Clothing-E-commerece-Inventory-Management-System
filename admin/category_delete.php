<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Admin\CategoryController;
$controller = new CategoryController();
$controller->categoryDelete();
