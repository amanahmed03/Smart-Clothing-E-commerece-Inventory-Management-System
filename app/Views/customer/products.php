<?php include APP_ROOT . "/app/Views/customer/customer_header.php"; ?>

<div style="margin-bottom: 28px;">
    <h1 style="font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 6px;">
        Smart Apparel Catalog
    </h1>
    <p style="color: var(--text-secondary); font-size: 15px;">
        High-performance wearable technology with integrated micro-sensors and temperature regulation.
    </p>
</div>

<!-- Search & Category Filter Toolbar -->
<div class="card" style="padding: 16px 20px; margin-bottom: 32px;">
    <form method="GET" action="products.php" style="display: flex; flex-wrap: wrap; gap: 12px; align-items: center; justify-content: space-between;">
        <div style="display: flex; gap: 8px; flex: 1; min-width: 260px;">
            <input 
                type="text" 
                name="search" 
                value="<?= e($searchQuery) ?>" 
                placeholder="Search smart jackets, tees, sensors..." 
                style="flex: 1; padding: 10px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #fff; font-size: 14px;"
            >
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            <?php if (!empty($searchQuery) || $selectedCategory > 0): ?>
                <a href="products.php" class="btn btn-sm" style="color: var(--text-secondary); text-decoration: none;">Clear</a>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 8px; flex-wrap: wrap; align-items: center;">
            <span style="font-size: 13px; color: var(--text-secondary);">Category:</span>
            <a href="products.php<?= !empty($searchQuery) ? '?search=' . urlencode($searchQuery) : '' ?>" 
               class="btn btn-sm <?= $selectedCategory === 0 ? 'btn-primary' : 'btn-secondary' ?>">
               All
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="products.php?category=<?= $cat['id'] ?><?= !empty($searchQuery) ? '&search=' . urlencode($searchQuery) : '' ?>" 
                   class="btn btn-sm <?= $selectedCategory === (int)$cat['id'] ? 'btn-primary' : 'btn-secondary' ?>">
                   <?= e($cat['name']) ?>
                </a>
            <?php endforeach; ?>
        </div>
    </form>
</div>

<!-- Product Cards Grid -->
<?php if (empty($products)): ?>
    <div class="card" style="text-align: center; padding: 48px 20px;">
        <div style="font-size: 40px; margin-bottom: 12px;">🔍</div>
        <h3 style="font-size: 18px; margin-bottom: 8px;">No products found</h3>
        <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 20px;">
            No items matched your current filter criteria.
        </p>
        <a href="products.php" class="btn btn-primary">Reset Filters</a>
    </div>
<?php else: ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px;">
        <?php foreach ($products as $item): ?>
            <div class="card" style="display: flex; flex-direction: column; justify-content: space-between; position: relative; overflow: hidden;">
                <!-- Product Card Body -->
                <div>
                    <!-- Visual Category Tag -->
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #818cf8; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">
                            <?= e($item['category_name']) ?>
                        </span>
                        <span style="font-size: 12px; padding: 2px 8px; border-radius: 4px; font-weight: 600; <?= $item['stock_quantity'] > 0 ? 'background: rgba(16, 185, 129, 0.15); color: var(--success);' : 'background: rgba(239, 68, 68, 0.15); color: var(--danger);' ?>">
                            <?= $item['stock_quantity'] > 0 ? 'In Stock: ' . $item['stock_quantity'] : 'Out of Stock' ?>
                        </span>
                    </div>

                    <!-- Product Mock Thumbnail -->
                    <div style="height: 140px; background: rgba(15, 23, 42, 0.8); border: 1px solid rgba(255,255,255,0.05); border-radius: 8px; margin-bottom: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 12px;">
                        <span style="font-size: 36px; margin-bottom: 6px;">⚡</span>
                        <span style="font-size: 11px; color: var(--text-secondary); font-family: Consolas, monospace;">
                            <?= e($item['color']) ?> &bull; <?= e($item['sizes']) ?>
                        </span>
                    </div>

                    <h3 style="font-size: 18px; font-weight: 600; margin-bottom: 8px; line-height: 1.3;">
                        <a href="<?= base_url('customer/product_details.php?id=' . $item['id']) ?>" style="color: #ffffff; text-decoration: none;">
                            <?= e($item['name']) ?>
                        </a>
                    </h3>

                    <p style="color: var(--text-secondary); font-size: 13px; line-height: 1.5; margin-bottom: 16px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        <?= e($item['description']) ?>
                    </p>
                </div>

                <!-- Product Card Footer & Actions -->
                <div>
                    <div style="display: flex; align-items: baseline; gap: 8px; margin-bottom: 16px;">
                        <span style="font-size: 22px; font-weight: 700; color: #ffffff;">
                            $<?= number_format($item['price'], 2) ?>
                        </span>
                    </div>

                    <div style="display: flex; gap: 10px;">
                        <a href="<?= base_url('customer/product_details.php?id=' . $item['id']) ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">
                            Details
                        </a>

                        <?php if ($item['stock_quantity'] > 0): ?>
                            <form method="POST" action="<?= base_url('customer/add_to_cart.php') ?>" style="flex: 1;">
                                <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn btn-primary" style="width: 100%;">
                                    + Cart
                                </button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-disabled" disabled style="flex: 1;">
                                Sold Out
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<?php include APP_ROOT . "/app/Views/customer/customer_footer.php"; ?>