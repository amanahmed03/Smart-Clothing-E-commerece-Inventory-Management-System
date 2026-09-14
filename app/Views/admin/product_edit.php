<?php include APP_ROOT . "/app/Views/admin/admin_header.php"; ?>

<?php if (!empty($flashSuccess)): ?>
<div class="alert alert-success"><span><?= e($flashSuccess) ?></span></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="alert alert-danger"><span><?= e($flashError) ?></span></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Product</h1>
        <p class="page-subtitle">Update details for "<?= e($product['name']) ?>".</p>
    </div>
    <a href="<?= base_url('admin/products.php') ?>" class="btn btn-secondary">
        &larr; Back to Products
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger" style="max-width: 640px;">
        <div>
            <?php foreach ($errors as $err): ?>
                <div>&bull; <?= e($err) ?></div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="form-card">
    <form method="POST" action="product_edit.php?id=<?= $productId ?>" novalidate>
        <div class="form-group">
            <label for="category_id" class="form-label">Category <span style="color: var(--danger);">*</span></label>
            <select id="category_id" name="category_id" class="form-control" required>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (int)$product['category_id'] === (int)$cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="name" class="form-label">Product Name <span style="color: var(--danger);">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= e($product['name']) ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="slug" class="form-label">URL Slug</label>
            <input type="text" id="slug" name="slug" class="form-control" value="<?= e($product['slug']) ?>">
        </div>

        <div class="form-group">
            <label for="price" class="form-label">Price ($) <span style="color: var(--danger);">*</span></label>
            <input type="number" id="price" name="price" class="form-control" value="<?= e($product['price']) ?>" step="0.01" min="0.01" required>
        </div>

        <div class="form-group">
            <label for="stock_quantity" class="form-label">Stock Quantity <span style="color: var(--danger);">*</span></label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" value="<?= (int)$product['stock_quantity'] ?>" min="0" required>
        </div>

        <div class="form-group">
            <label for="sizes" class="form-label">Available Sizes</label>
            <input type="text" id="sizes" name="sizes" class="form-control" value="<?= e($product['sizes']) ?>">
        </div>

        <div class="form-group">
            <label for="color" class="form-label">Primary Color</label>
            <input type="text" id="color" name="color" class="form-control" value="<?= e($product['color']) ?>">
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="4" class="form-control"><?= e($product['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label for="smart_features" class="form-label">Smart Features</label>
            <textarea id="smart_features" name="smart_features" rows="3" class="form-control"><?= e($product['smart_features']) ?></textarea>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" <?= (int)$product['is_active'] ? 'checked' : '' ?> style="width: auto;">
                <span style="font-size: 14px; color: #cbd5e1;">Product is active (visible to customers)</span>
            </label>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 28px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= base_url('admin/products.php') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>


<?php include APP_ROOT . "/app/Views/admin/admin_footer.php"; ?>