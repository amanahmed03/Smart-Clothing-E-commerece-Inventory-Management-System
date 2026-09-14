<?php include APP_ROOT . "/app/Views/admin/admin_header.php"; ?>

<?php if (!empty($flashSuccess)): ?>
<div class="alert alert-success"><span><?= e($flashSuccess) ?></span></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="alert alert-danger"><span><?= e($flashError) ?></span></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Add New Product</h1>
        <p class="page-subtitle">Add a new smart apparel item to the catalog.</p>
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
    <form method="POST" action="product_add.php" novalidate>
        <div class="form-group">
            <label for="category_id" class="form-label">Category <span style="color: var(--danger);">*</span></label>
            <select id="category_id" name="category_id" class="form-control" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= ((int)($categoryId ?? '')) === (int)$cat['id'] ? 'selected' : '' ?>>
                        <?= e($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="name" class="form-label">Product Name <span style="color: var(--danger);">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= e($name) ?>" required autofocus placeholder="e.g. Smart Thermal Jacket">
        </div>

        <div class="form-group">
            <label for="slug" class="form-label">URL Slug</label>
            <input type="text" id="slug" name="slug" class="form-control" value="<?= e($slug ?? '') ?>" placeholder="Auto-generated from name">
            <div class="form-help">A URL-friendly version of the product name.</div>
        </div>

        <div class="form-group">
            <label for="price" class="form-label">Price ($) <span style="color: var(--danger);">*</span></label>
            <input type="number" id="price" name="price" class="form-control" value="<?= e($price) ?>" step="0.01" min="0.01" required placeholder="0.00">
        </div>

        <div class="form-group">
            <label for="stock_quantity" class="form-label">Stock Quantity <span style="color: var(--danger);">*</span></label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" value="<?= e($stock_quantity) ?>" min="0" required placeholder="0">
        </div>

        <div class="form-group">
            <label for="sizes" class="form-label">Available Sizes</label>
            <input type="text" id="sizes" name="sizes" class="form-control" value="<?= e($sizes) ?>" placeholder="e.g. S,M,L,XL,XXL">
            <div class="form-help">Comma-separated size values.</div>
        </div>

        <div class="form-group">
            <label for="color" class="form-label">Primary Color</label>
            <input type="text" id="color" name="color" class="form-control" value="<?= e($color) ?>" placeholder="e.g. Obsidian Black">
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="4" class="form-control" placeholder="Detailed product description..."><?= e($description) ?></textarea>
        </div>

        <div class="form-group">
            <label for="smart_features" class="form-label">Smart Features</label>
            <textarea id="smart_features" name="smart_features" rows="3" class="form-control" placeholder="List the smart sensors and features..."><?= e($smart_features) ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 28px;">
            <button type="submit" class="btn btn-primary">Create Product</button>
            <a href="<?= base_url('admin/products.php') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>


<?php include APP_ROOT . "/app/Views/admin/admin_footer.php"; ?>