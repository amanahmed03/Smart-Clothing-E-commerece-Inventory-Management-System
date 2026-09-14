<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Admin\UserController;
$controller = new UserController();
$controller->userEdit();
