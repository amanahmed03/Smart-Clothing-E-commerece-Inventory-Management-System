<?php include APP_ROOT . "/app/Views/inventory/inventory_header.php"; ?>

<?php
$pageTitle = 'Inventory Dashboard';

$totalProductsStmt = $pdo->query("SELECT COUNT(*) AS total FROM products");
$totalProducts = (int)$totalProductsStmt->fetchColumn();

$lowStockStmt = $pdo->query("SELECT COUNT(*) AS total FROM products WHERE stock_quantity < 5");
$lowStock = (int)$lowStockStmt->fetchColumn();

$outOfStockStmt = $pdo->query("SELECT COUNT(*) AS total FROM products WHERE stock_quantity = 0");
$outOfStock = (int)$outOfStockStmt->fetchColumn();

$totalStockValueStmt = $pdo->query("SELECT COALESCE(SUM(price * stock_quantity), 0) AS total FROM products");
$totalStockValue = (float)$totalStockValueStmt->fetchColumn();

// Fetch low stock items
$lowStockItemsStmt = $pdo->query("
    SELECT p.id, p.name, p.stock_quantity, c.name AS category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    WHERE p.stock_quantity < 5 AND p.is_active = 1
    ORDER BY p.stock_quantity ASC
    LIMIT 10
");
$lowStockItems = $lowStockItemsStmt->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Inventory Dashboard</h1>
        <p class="page-subtitle">Monitor stock levels and manage product inventory.</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-title">Total Products</div>
            <div class="stat-value"><?= $totalProducts ?></div>
        </div>
        <div class="stat-icon">👕</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Low Stock (&lt; 5)</div>
            <div class="stat-value" style="color: var(--warning);"><?= $lowStock ?></div>
        </div>
        <div class="stat-icon">⚠️</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Out of Stock</div>
            <div class="stat-value" style="color: var(--danger);"><?= $outOfStock ?></div>
        </div>
        <div class="stat-icon">🚫</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Total Stock Value</div>
            <div class="stat-value">$<?= number_format($totalStockValue, 2) ?></div>
        </div>
        <div class="stat-icon">💰</div>
    </div>
</div>

<!-- Quick Actions -->
<div style="margin-bottom: 24px;">
    <a href="<?= base_url('inventory/products.php') ?>" class="btn btn-primary" style="padding: 12px 28px;">
        📦 Manage Products &rarr;
    </a>
</div>

<!-- Low Stock Alert -->
<?php if (!empty($lowStockItems)): ?>
    <div class="table-card" style="margin-bottom: 24px;">
        <div style="padding: 18px 20px; border-bottom: 1px solid var(--border-color);">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px;">⚠️ Low Stock Alerts</h3>
        </div>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lowStockItems as $item): ?>
                        <tr>
                            <td><strong style="color: #fff;"><?= e($item['name']) ?></strong></td>
                            <td><?= e($item['category_name']) ?></td>
                            <td style="color: var(--danger); font-weight: 700;"><?= (int)$item['stock_quantity'] ?> units</td>
                            <td>
                                <a href="<?= base_url('inventory/product_edit.php?id=' . $item['id']) ?>" class="btn btn-primary btn-sm">
                                    Update Stock
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Recent Products -->
<div class="table-card">
    <div style="padding: 18px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px;">Recent Products</h3>
        <a href="<?= base_url('inventory/products.php') ?>" style="color: var(--accent); font-size: 13px; text-decoration: none; font-weight: 600;">
            View All &rarr;
        </a>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $recentProductsStmt = $pdo->query("SELECT p.id, p.name, c.name AS category_name, p.price, p.stock_quantity, p.is_active FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC LIMIT 10");
                $recentProducts = $recentProductsStmt->fetchAll();
                foreach ($recentProducts as $p): ?>
                    <tr>
                        <td><strong style="color: #fff;"><?= e($p['name']) ?></strong></td>
                        <td><?= e($p['category_name']) ?></td>
                        <td>$<?= number_format($p['price'], 2) ?></td>
                        <td style="color: <?= (int)$p['stock_quantity'] < 5 ? 'var(--danger)' : 'var(--success)' ?>; font-weight: 600;"><?= (int)$p['stock_quantity'] ?></td>
                        <td><span class="badge <?= (int)$p['is_active'] ? 'badge-active' : 'badge-inactive' ?>"><?= (int)$p['is_active'] ? 'Active' : 'Inactive' ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_ROOT . "/app/Views/inventory/inventory_footer.php"; ?>