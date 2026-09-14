<?php include APP_ROOT . "/app/Views/inventory/inventory_header.php"; ?>

<?php
$productId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$productId || $productId <= 0) {
    set_flash('error', 'Invalid product ID.');
    redirect(base_url('inventory/products.php'));
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $productId]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('error', 'Product not found.');
    redirect(base_url('inventory/products.php'));
}

$errors = [];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Stock</h1>
        <p class="page-subtitle">Update stock for "<?= e($product['name']) ?>".</p>
    </div>
    <a href="<?= base_url('inventory/products.php') ?>" class="btn btn-secondary">
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
            <label class="form-label">Product Name</label>
            <input type="text" class="form-control" value="<?= e($product['name']) ?>" readonly>
        </div>

        <div class="form-group">
            <label for="category" class="form-label">Category</label>
            <input type="text" class="form-control" value="<?= e($product['category_name'] ?? '') ?>" readonly>
        </div>

        <div class="form-group">
            <label for="price" class="form-label">Price</label>
            <input type="text" class="form-control" value="$<?= number_format($product['price'], 2) ?>" readonly>
        </div>

        <div class="form-group">
            <label for="current_stock" class="form-label">Current Stock</label>
            <input type="text" class="form-control" value="<?= (int)$product['stock_quantity'] ?>" readonly>
        </div>

        <div class="form-group">
            <label for="stock_quantity" class="form-label">New Stock Quantity <span style="color: var(--danger);">*</span></label>
            <input type="number" id="stock_quantity" name="stock_quantity" class="form-control" value="<?= (int)$product['stock_quantity'] ?>" min="0" required>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                <input type="checkbox" name="is_active" value="1" <?= (int)$product['is_active'] ? 'checked' : '' ?> style="width: auto;">
                <span style="font-size: 14px; color: #cbd5e1;">Product is active</span>
            </label>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 28px;">
            <button type="submit" class="btn btn-primary">Update Stock</button>
            <a href="<?= base_url('inventory/products.php') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php include APP_ROOT . "/app/Views/inventory/inventory_footer.php"; ?>