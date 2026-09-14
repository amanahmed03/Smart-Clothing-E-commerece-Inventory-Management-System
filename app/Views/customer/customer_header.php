<?php
$currentPage = basename($_SERVER['PHP_SELF'] ?? '');
$cartCount = $cartCount ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?>Smart Clothing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b1120;
            --surface-color: #1e293b;
            --surface-light: #334155;
            --border-color: #2e3c51;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --accent-light: rgba(99, 102, 241, 0.15);
            --success: #10b981;
            --success-bg: rgba(16, 185, 129, 0.12);
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --warning: #f59e0b;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background-color: var(--bg-color);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation Bar */
        .navbar {
            background-color: rgba(30, 41, 59, 0.95);
            backdrop-filter: blur(8px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-container {
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 20px;
            height: 68px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .brand-logo span {
            color: var(--accent);
        }

        .nav-menu {
            display: flex;
            align-items: center;
            list-style: none;
            gap: 8px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: 8px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: var(--text-primary);
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-link.active {
            color: #ffffff;
            background-color: var(--accent-light);
            border: 1px solid rgba(99, 102, 241, 0.3);
        }

        .cart-badge {
            background-color: var(--accent);
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 999px;
            min-width: 18px;
            text-align: center;
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 10px;
            padding-left: 12px;
            border-left: 1px solid var(--border-color);
        }

        .user-name {
            font-size: 13px;
            color: var(--text-primary);
            font-weight: 600;
        }

        .btn-nav-logout {
            padding: 6px 12px;
            font-size: 13px;
            color: #fca5a5;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 6px;
            text-decoration: none;
            transition: background 0.2s;
        }

        .btn-nav-logout:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Content Container */
        .main-content {
            max-width: 1140px;
            margin: 0 auto;
            padding: 32px 20px 60px;
            width: 100%;
            flex: 1;
        }

        /* Standard Alerts */
        .alert {
            padding: 14px 18px;
            border-radius: 10px;
            margin-bottom: 24px;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .alert-success {
            background-color: var(--success-bg);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #6ee7b7;
        }

        .alert-error {
            background-color: var(--danger-bg);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease;
        }

        .btn-primary {
            background-color: var(--accent);
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: var(--accent-hover);
        }

        .btn-secondary {
            background-color: var(--surface-light);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background-color: #475569;
        }

        .btn-danger {
            background-color: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
        }

        .btn-danger:hover {
            background-color: #ef4444;
            color: #ffffff;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 13px;
        }

        .btn-disabled {
            background-color: #1e293b !important;
            color: #64748b !important;
            cursor: not-allowed !important;
            border: 1px solid #334155 !important;
        }

        /* Card container */
        .card {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 24px;
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .nav-container {
                height: auto;
                padding: 12px 16px;
                flex-direction: column;
                gap: 12px;
            }
            .nav-menu {
                flex-wrap: wrap;
                justify-content: center;
            }
            .user-pill {
                border-left: none;
                padding-left: 0;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="<?= base_url('customer/dashboard.php') ?>" class="brand-logo">
                Smart<span>Clothing</span>
            </a>

            <ul class="nav-menu">
                <li>
                    <a href="<?= base_url('customer/dashboard.php') ?>" class="nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                        Home
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('customer/products.php') ?>" class="nav-link <?= ($currentPage === 'products.php' || $currentPage === 'product_details.php') ? 'active' : '' ?>">
                        Products
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('customer/cart.php') ?>" class="nav-link <?= $currentPage === 'cart.php' ? 'active' : '' ?>">
                        Cart
                        <?php if ($cartCount > 0): ?>
                            <span class="cart-badge"><?= $cartCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('customer/orders.php') ?>" class="nav-link <?= in_array($currentPage, ['orders.php', 'order_detail.php']) ? 'active' : '' ?>">
                        My Orders
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('customer/profile.php') ?>" class="nav-link <?= $currentPage === 'profile.php' ? 'active' : '' ?>">
                        Profile
                    </a>
                </li>
                <li class="user-pill">
                    <span class="user-name"><?= e($currentUser['name'] ?? 'Guest') ?></span>
                    <a href="<?= base_url('logout.php') ?>" class="btn-nav-logout">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <main class="main-content">
        <?php if (!empty($flashSuccess)): ?>
            <div class="alert alert-success">
                <span><?= e($flashSuccess) ?></span>
            </div>
        <?php endif; ?>

        <?php if (!empty($flashError)): ?>
            <div class="alert alert-error">
                <span><?= e($flashError) ?></span>
            </div>
        <?php endif; ?>
