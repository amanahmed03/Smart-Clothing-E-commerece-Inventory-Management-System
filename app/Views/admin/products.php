<?php include APP_ROOT . "/app/Views/admin/admin_header.php"; ?>

<?php if (!empty($flashSuccess)): ?>
<div class="alert alert-success"><span><?= e($flashSuccess) ?></span></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="alert alert-danger"><span><?= e($flashError) ?></span></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Product Management</h1>
        <p class="page-subtitle">View, edit, and manage the smart apparel catalog.</p>
    </div>
    <a href="<?= base_url('admin/product_add.php') ?>" class="btn btn-primary">
        ➕ Add New Product
    </a>
</div>

<div class="table-card">
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th style="text-align: right; width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No products found. Click "Add New Product" above to create one.
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
                                <span style="color: <?= (int)$p['stock_quantity'] > 0 ? 'var(--success)' : 'var(--danger)' ?>; font-weight: 600;">
                                    <?= (int)$p['stock_quantity'] ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= (int)$p['is_active'] ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= (int)$p['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td style="font-size: 12px; color: var(--text-muted); white-space: nowrap;">
                                <?= date('M j, Y', strtotime($p['created_at'])) ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= base_url('admin/product_edit.php?id=' . $p['id']) ?>" class="btn btn-secondary btn-sm">
                                    Edit
                                </a>
                                <a 
                                    href="<?= base_url('admin/product_delete.php?id=' . $p['id']) ?>" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete \"<?= htmlspecialchars(addslashes($p['name']), ENT_QUOTES) ?>\"?');"
                                >
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<?php include APP_ROOT . "/app/Views/admin/admin_footer.php"; ?>