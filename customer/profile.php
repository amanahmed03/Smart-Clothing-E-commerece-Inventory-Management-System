<?php
require_once __DIR__ . '/../public/bootstrap.php';
use App\Controllers\Customer\ProfileController;
$controller = new ProfileController();
$controller->profile();
