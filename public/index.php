<?php
/**
 * Smart Clothing E-Commerce Management System
 * Web Application Entry Point
 * Front Controller Pattern
 */
require_once __DIR__ . '/bootstrap.php';

use App\Core\Router;

$router = new Router();

// Auth routes
$router->add('login', 'App\Controllers\AuthController', 'login');
$router->add('register', 'App\Controllers\AuthController', 'register');
$router->add('logout', 'App\Controllers\AuthController', 'logout');

// Admin routes
$router->add('admin/dashboard', 'App\Controllers\Admin\DashboardController', 'dashboard');
$router->add('admin/products', 'App\Controllers\Admin\ProductController', 'products');
$router->add('admin/product_add', 'App\Controllers\Admin\ProductController', 'productAdd');
$router->add('admin/product_edit', 'App\Controllers\Admin\ProductController', 'productEdit');
$router->add('admin/product_delete', 'App\Controllers\Admin\ProductController', 'productDelete');
$router->add('admin/categories', 'App\Controllers\Admin\CategoryController', 'categories');
$router->add('admin/category_add', 'App\Controllers\Admin\CategoryController', 'categoryAdd');
$router->add('admin/category_edit', 'App\Controllers\Admin\CategoryController', 'categoryEdit');
$router->add('admin/category_delete', 'App\Controllers\Admin\CategoryController', 'categoryDelete');
$router->add('admin/users', 'App\Controllers\Admin\UserController', 'users');
$router->add('admin/user_edit', 'App\Controllers\Admin\UserController', 'userEdit');
$router->add('admin/user_delete', 'App\Controllers\Admin\UserController', 'userDelete');

// Customer routes
$router->add('customer/dashboard', 'App\Controllers\Customer\DashboardController', 'dashboard');
$router->add('customer/products', 'App\Controllers\Customer\ProductController', 'products');
$router->add('customer/product_details', 'App\Controllers\Customer\ProductController', 'productDetails');
$router->add('customer/cart', 'App\Controllers\Customer\CartController', 'cart');
$router->add('customer/add_to_cart', 'App\Controllers\Customer\CartController', 'addToCart');
$router->add('customer/update_cart', 'App\Controllers\Customer\CartController', 'updateCart');
$router->add('customer/remove_from_cart', 'App\Controllers\Customer\CartController', 'removeFromCart');
$router->add('customer/checkout', 'App\Controllers\Customer\OrderController', 'checkout');
$router->add('customer/orders', 'App\Controllers\Customer\OrderController', 'orders');
$router->add('customer/order_detail', 'App\Controllers\Customer\OrderController', 'orderDetail');
$router->add('customer/profile', 'App\Controllers\Customer\ProfileController', 'profile');

// Delivery routes
$router->add('delivery/dashboard', 'App\Controllers\Delivery\DashboardController', 'dashboard');
$router->add('delivery/orders', 'App\Controllers\Delivery\OrderController', 'orders');
$router->add('delivery/order_detail', 'App\Controllers\Delivery\OrderController', 'orderDetail');

// Inventory routes
$router->add('inventory/dashboard', 'App\Controllers\Inventory\DashboardController', 'dashboard');
$router->add('inventory/products', 'App\Controllers\Inventory\ProductController', 'products');
$router->add('inventory/product_edit', 'App\Controllers\Inventory\ProductController', 'productEdit');

$router->dispatch();
