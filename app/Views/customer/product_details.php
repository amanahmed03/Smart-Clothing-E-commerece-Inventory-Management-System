<?php include APP_ROOT . "/app/Views/customer/customer_header.php"; ?>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('customer/products.php') ?>" style="color: var(--text-secondary); text-decoration: none; font-size: 14px;">
        &larr; Back to Catalog
    </a>
</div>

<div class="card" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 40px; padding: 36px;">
    <!-- Left Column: Product Visual/Badge -->
    <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; background: rgba(15, 23, 42, 0.7); border: 1px solid var(--border-color); border-radius: 12px; min-height: 320px; padding: 24px; text-align: center;">
        <div style="font-size: 72px; margin-bottom: 16px;">🧥</div>
        <div style="font-size: 13px; color: #818cf8; text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; margin-bottom: 6px;">
            Smart Wearable Technology
        </div>
        <div style="font-size: 14px; color: var(--text-secondary);">
            Color: <strong><?= e($product['color']) ?></strong>
        </div>
    </div>

    <!-- Right Column: Details and Add to Cart Form -->
    <div>
        <span style="display: inline-block; font-size: 12px; color: #818cf8; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 700; margin-bottom: 8px;">
            <?= e($product['category_name'] ?? 'Uncategorized') ?>
        </span>

        <h1 style="font-family: 'Outfit', sans-serif; font-size: 30px; font-weight: 700; margin-bottom: 12px; line-height: 1.25;">
            <?= e($product['name']) ?>
        </h1>

        <div style="font-size: 28px; font-weight: 700; color: #ffffff; margin-bottom: 20px;">
            $<?= number_format($product['price'], 2) ?>
        </div>

        <div style="margin-bottom: 24px;">
            <h4 style="font-size: 14px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 8px; letter-spacing: 0.05em;">
                Overview
            </h4>
            <p style="color: #cbd5e1; font-size: 15px; line-height: 1.6;">
                <?= nl2br(e($product['description'])) ?>
            </p>
        </div>

        <?php if (!empty($product['smart_features'])): ?>
            <div style="margin-bottom: 24px; padding: 16px; background: rgba(15, 23, 42, 0.5); border: 1px solid var(--border-color); border-radius: 8px;">
                <h4 style="font-size: 13px; text-transform: uppercase; color: #a5b4fc; margin-bottom: 6px; letter-spacing: 0.05em;">
                    ⚡ Smart Features & Sensors
                </h4>
                <p style="color: var(--text-primary); font-size: 14px; line-height: 1.5;">
                    <?= e($product['smart_features']) ?>
                </p>
            </div>
        <?php endif; ?>

        <div style="margin-bottom: 24px; display: flex; align-items: center; gap: 12px;">
            <span style="font-size: 14px; color: var(--text-secondary);">Stock Status:</span>
            <?php if ($product['stock_quantity'] > 0): ?>
                <span style="padding: 4px 10px; border-radius: 999px; background: rgba(16, 185, 129, 0.15); color: var(--success); font-size: 13px; font-weight: 600; border: 1px solid rgba(16, 185, 129, 0.3);">
                    In Stock (<?= (int)$product['stock_quantity'] ?> units available)
                </span>
            <?php else: ?>
                <span style="padding: 4px 10px; border-radius: 999px; background: rgba(239, 68, 68, 0.15); color: var(--danger); font-size: 13px; font-weight: 600; border: 1px solid rgba(239, 68, 68, 0.3);">
                    Out of Stock
                </span>
            <?php endif; ?>
        </div>

        <?php if ($product['stock_quantity'] > 0): ?>
            <form method="POST" action="<?= base_url('customer/add_to_cart.php') ?>" style="margin-top: 24px; border-top: 1px solid var(--border-color); padding-top: 24px;">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <!-- Size Selection -->
                <?php if (!empty($availableSizes)): ?>
                    <div style="margin-bottom: 20px;">
                        <label for="selected_size" style="display: block; font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">
                            Select Size
                        </label>
                        <select name="selected_size" id="selected_size" style="padding: 10px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #ffffff; font-size: 14px; min-width: 140px;">
                            <?php foreach ($availableSizes as $size): ?>
                                <option value="<?= e($size) ?>"><?= e($size) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <!-- Quantity Selection -->
                <div style="margin-bottom: 24px;">
                    <label for="quantity" style="display: block; font-size: 13px; color: var(--text-secondary); margin-bottom: 8px; font-weight: 600;">
                        Quantity
                    </label>
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <input 
                            type="number" 
                            id="quantity" 
                            name="quantity" 
                            value="1" 
                            min="1" 
                            max="<?= min(10, (int)$product['stock_quantity']) ?>" 
                            required
                            style="width: 90px; padding: 10px 12px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #ffffff; font-size: 14px; text-align: center;"
                        >
                        <span style="font-size: 13px; color: var(--text-secondary);">
                            (Max: <?= min(10, (int)$product['stock_quantity']) ?> per order)
                        </span>
                    </div>
                </div>

                <div style="display: flex; gap: 14px;">
                    <button type="submit" class="btn btn-primary" style="padding: 12px 28px; font-size: 15px;">
                        🛒 Add to Cart
                    </button>
                    <a href="<?= base_url('customer/products.php') ?>" class="btn btn-secondary">
                        Continue Browsing
                    </a>
                </div>
            </form>
        <?php else: ?>
            <div style="margin-top: 24px; padding: 16px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 8px; color: #fca5a5; font-size: 14px;">
                This product is currently out of stock. Please check back later or explore our other smart apparel.
            </div>
            <div style="margin-top: 20px;">
                <a href="<?= base_url('customer/products.php') ?>" class="btn btn-secondary">
                    Back to Products
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>


<?php include APP_ROOT . "/app/Views/customer/customer_footer.php"; ?>