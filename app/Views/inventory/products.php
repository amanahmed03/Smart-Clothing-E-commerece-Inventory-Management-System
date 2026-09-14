<?php include APP_ROOT . "/app/Views/inventory/inventory_header.php"; ?>

<?php
$pageTitle = 'Product Stock Management';

// Fetch all products with category names and stock info
$stmt = $pdo->query("
    SELECT p.id, p.name, p.price, p.stock_quantity, p.is_active,
           c.name AS category_name, p.sizes, p.color
    FROM products p
    JOIN categories c ON p.category_id = c.id
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll();

// Calculate total stock value
$totalStockValue = 0;
$totalItems = 0;
foreach ($products as $p) {
    $totalStockValue += $p['price'] * (int)$p['stock_quantity'];
    $totalItems += (int)$p['stock_quantity'];
}
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Product & Stock Management</h1>
        <p class="page-subtitle">Monitor inventory levels and manage product stock.</p>
    </div>
</div>

<!-- Stats Summary -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-title">Total Products</div>
            <div class="stat-value"><?= count($products) ?></div>
        </div>
        <div class="stat-icon">👕</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Total Stock Items</div>
            <div class="stat-value"><?= $totalItems ?></div>
        </div>
        <div class="stat-icon">📦</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Stock Value</div>
            <div class="stat-value">$<?= number_format($totalStockValue, 2) ?></div>
        </div>
        <div class="stat-icon">💰</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Low Stock Items</div>
            <div class="stat-value" style="color: var(--danger);">
                <?= count(array_filter($products, fn($p) => (int)$p['stock_quantity'] < 5)) ?>
            </div>
        </div>
        <div class="stat-icon">⚠️</div>
    </div>
</div>

<div class="table-card">
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Sizes</th>
                    <th>Color</th>
                    <th>Status</th>
                    <th style="text-align: right; width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No products found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>#<?= (int)$p['id'] ?></td>
                            <td>
                                <strong style="color: #fff;"><?= e($p['name']) ?></strong>
                            </td>
                            <td><?= e($p['category_name']) ?></td>
                            <td>$<?= number_format($p['price'], 2) ?></td>
                            <td>
                                <span style="color: <?= (int)$p['stock_quantity'] < 5 ? 'var(--danger)' : 'var(--success)' ?>; font-weight: 600;">
                                    <?= (int)$p['stock_quantity'] ?>
                                </span>
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted);"><?= e($p['sizes']) ?></td>
                            <td><?= e($p['color']) ?></td>
                            <td>
                                <span class="badge <?= (int)$p['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= (int)$p['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= base_url('inventory/product_edit.php?id=' . $p['id']) ?>" class="btn btn-secondary btn-sm">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_ROOT . "/app/Views/inventory/inventory_footer.php"; ?>