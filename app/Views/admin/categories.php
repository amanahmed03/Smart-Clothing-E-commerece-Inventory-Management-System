<?php include APP_ROOT . "/app/Views/admin/admin_header.php"; ?>

<?php if (!empty($flashSuccess)): ?>
<div class="alert alert-success"><span><?= e($flashSuccess) ?></span></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="alert alert-danger"><span><?= e($flashError) ?></span></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Category Management</h1>
        <p class="page-subtitle">Organize smart clothing into logical catalog classifications.</p>
    </div>
    <a href="<?= base_url('admin/category_add.php') ?>" class="btn btn-primary">
        ➕ Add New Category
    </a>
</div>

<div class="table-card">
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Category Name</th>
                    <th>URL Slug</th>
                    <th>Assigned Products</th>
                    <th>Description</th>
                    <th style="text-align: right; width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No categories found. Click "Add New Category" above to create one.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>#<?= (int)$cat['id'] ?></td>
                            <td>
                                <strong style="color: #fff; font-size: 15px;"><?= e($cat['name']) ?></strong>
                            </td>
                            <td>
                                <code style="font-size: 12px;"><?= e($cat['slug']) ?></code>
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(99, 102, 241, 0.15); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.3);">
                                    <?= (int)$cat['product_count'] ?> Products
                                </span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 13px; max-width: 320px;">
                                <?= e($cat['description'] ?: '—') ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= base_url('admin/category_edit.php?id=' . $cat['id']) ?>" class="btn btn-secondary btn-sm">
                                    Edit
                                </a>
                                <a 
                                    href="<?= base_url('admin/category_delete.php?id=' . $cat['id']) ?>" 
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete category \'<?= htmlspecialchars(addslashes($cat['name']), ENT_QUOTES) ?>\'?');"
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