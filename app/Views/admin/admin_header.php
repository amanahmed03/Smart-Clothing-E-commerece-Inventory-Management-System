<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    if (!isset($currentPage)) {
        $currentPage = basename($_SERVER['PHP_SELF'] ?? '');
    }
    ?>
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?>Admin Portal - Smart Clothing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('admin/assets/admin.css') ?>">
</head>
<body>
    <header class="admin-navbar">
        <div class="admin-nav-container">
            <a href="<?= base_url('admin/dashboard.php') ?>" class="admin-brand">
                Smart<span>Clothing</span> &bull; <small style="font-size: 12px; color: var(--danger); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Admin</small>
            </a>

            <nav>
                <ul class="admin-nav-links">
                    <li>
                        <a href="<?= base_url('admin/dashboard.php') ?>" class="admin-nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                            Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/users.php') ?>" class="admin-nav-link <?= in_array($currentPage, ['users.php', 'user_edit.php']) ? 'active' : '' ?>">
                            Users
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/categories.php') ?>" class="admin-nav-link <?= in_array($currentPage, ['categories.php', 'category_add.php', 'category_edit.php']) ? 'active' : '' ?>">
                            Categories
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('admin/products.php') ?>" class="admin-nav-link <?= in_array($currentPage, ['products.php', 'product_add.php', 'product_edit.php', 'product_delete.php']) ? 'active' : '' ?>">
                            Products
                        </a>
                    </li>
                    <li class="admin-user-info">
                        <span style="font-size: 13px; font-weight: 600; color: #cbd5e1;"><?= e($adminUser['name']) ?></span>
                        <a href="<?= base_url('logout.php') ?>" class="btn btn-danger btn-sm">
                            Logout
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="admin-main">
