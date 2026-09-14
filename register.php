<?php
require_once __DIR__ . '/public/bootstrap.php';
use App\Controllers\AuthController;

$controller = new AuthController();
$controller->register();
