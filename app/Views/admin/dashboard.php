<?php include APP_ROOT . "/app/Views/admin/admin_header.php"; ?>

<?php if (!empty($flashSuccess)): ?>
<div class="alert alert-success"><span><?= e($flashSuccess) ?></span></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="alert alert-danger"><span><?= e($flashError) ?></span></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Admin Dashboard</h1>
        <p class="page-subtitle">Welcome back, <?= e($adminUser['name']) ?>. Here is the operational summary of the system.</p>
    </div>
</div>

<!-- Stats Grid (7 Metrics as requested) -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-title">Total Users</div>
            <div class="stat-value"><?= $totalUsers ?></div>
        </div>
        <div class="stat-icon">👥</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Total Customers</div>
            <div class="stat-value"><?= $totalCustomers ?></div>
        </div>
        <div class="stat-icon">🛍️</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Inventory Managers</div>
            <div class="stat-value"><?= $totalInventoryManagers ?></div>
        </div>
        <div class="stat-icon">📦</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Delivery Managers</div>
            <div class="stat-value"><?= $totalDeliveryManagers ?></div>
        </div>
        <div class="stat-icon">🚚</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Total Products</div>
            <div class="stat-value"><?= $totalProducts ?></div>
        </div>
        <div class="stat-icon">👕</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Total Categories</div>
            <div class="stat-value"><?= $totalCategories ?></div>
        </div>
        <div class="stat-icon">🏷️</div>
    </div>

    <div class="stat-card">
        <div>
            <div class="stat-title">Total Stock Quantity</div>
            <div class="stat-value"><?= number_format($totalStock) ?></div>
        </div>
        <div class="stat-icon">📊</div>
    </div>
</div>

<!-- Quick Navigation & Recent Activity Section -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 24px;">
    <!-- Quick Management Links -->
    <div class="table-card" style="padding: 24px;">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            Quick Management Links
        </h3>
        <div style="display: flex; flex-direction: column; gap: 12px;">
            <a href="<?= base_url('admin/users.php') ?>" class="btn btn-secondary" style="justify-content: flex-start; padding: 12px 16px;">
                👥 Manage System Users &rarr;
            </a>
            <a href="<?= base_url('admin/categories.php') ?>" class="btn btn-secondary" style="justify-content: flex-start; padding: 12px 16px;">
                🏷️ Manage Clothing Categories &rarr;
            </a>
            <a href="<?= base_url('admin/products.php') ?>" class="btn btn-secondary" style="justify-content: flex-start; padding: 12px 16px;">
                👕 Manage Smart Apparel Catalog &rarr;
            </a>
            <a href="<?= base_url('admin/product_add.php') ?>" class="btn btn-primary" style="justify-content: flex-start; padding: 12px 16px;">
                ➕ Add New Product &rarr;
            </a>
        </div>
    </div>

    <!-- Recent Users Table -->
    <div class="table-card">
        <div style="padding: 18px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px;">Recently Added Users</h3>
            <a href="<?= base_url('admin/users.php') ?>" style="color: var(--accent); font-size: 13px; text-decoration: none; font-weight: 600;">
                View All &rarr;
            </a>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Role</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentUsers as $u): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #fff;"><?= e($u['name']) ?></div>
                            <div style="font-size: 12px; color: var(--text-muted);"><?= e($u['email']) ?></div>
                        </td>
                        <td>
                            <span class="badge badge-<?= str_replace('_', '', $u['role']) ?>">
                                <?= e($u['role']) ?>
                            </span>
                        </td>
                        <td style="font-size: 12px; color: var(--text-muted); white-space: nowrap;">
                            <?= date('M j, Y', strtotime($u['created_at'])) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>


<?php include APP_ROOT . "/app/Views/admin/admin_footer.php"; ?>