<?php include APP_ROOT . "/app/Views/customer/customer_header.php"; ?>

<div style="margin-bottom: 32px;">
    <h1 style="font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 8px;">
        Welcome back, <?= e($currentUser['name'] ?? 'Customer') ?>!
    </h1>
    <p style="color: var(--text-secondary); font-size: 15px;">
        Explore the latest high-tech smart apparel, manage your shopping cart, and update your account details.
    </p>
</div>

<!-- Quick Action Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px; margin-bottom: 40px;">
    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="font-size: 28px; margin-bottom: 12px;">👕</div>
            <h3 style="font-size: 18px; margin-bottom: 6px;">Browse Catalog</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                Discover smart jackets, biometric tops, heated apparel, and athletic wear.
            </p>
        </div>
        <a href="<?= base_url('customer/products.php') ?>" class="btn btn-primary" style="align-self: flex-start;">
            View Products &rarr;
        </a>
    </div>

    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="font-size: 28px; margin-bottom: 12px;">🛒</div>
            <h3 style="font-size: 18px; margin-bottom: 6px;">Shopping Cart</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                You currently have <strong><?= $cartCount ?></strong> item(s) waiting in your cart.
            </p>
        </div>
        <a href="<?= base_url('customer/cart.php') ?>" class="btn btn-secondary" style="align-self: flex-start;">
            Review Cart &rarr;
        </a>
    </div>

    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="font-size: 28px; margin-bottom: 12px;">📦</div>
            <h3 style="font-size: 18px; margin-bottom: 6px;">My Orders</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                Track your past and current order deliveries.
            </p>
        </div>
        <a href="<?= base_url('customer/orders.php') ?>" class="btn btn-secondary" style="align-self: flex-start;">
            View Orders &rarr;
        </a>
    </div>

    <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
        <div>
            <div style="font-size: 28px; margin-bottom: 12px;">👤</div>
            <h3 style="font-size: 18px; margin-bottom: 6px;">My Profile</h3>
            <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 16px;">
                Update your contact details and view your account credentials.
            </p>
        </div>
        <a href="<?= base_url('customer/profile.php') ?>" class="btn btn-secondary" style="align-self: flex-start;">
            Edit Profile &rarr;
        </a>
    </div>
</div>

<!-- Featured Products Section -->
<div style="margin-bottom: 24px; display: flex; align-items: center; justify-content: space-between;">
    <h2 style="font-family: 'Outfit', sans-serif; font-size: 22px;">Featured Smart Apparel</h2>
    <a href="<?= base_url('customer/products.php') ?>" style="color: var(--accent); font-size: 14px; text-decoration: none; font-weight: 600;">
        See All Products &rarr;
    </a>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 20px;">
    <?php foreach ($featuredProducts as $item): ?>
        <div class="card" style="display: flex; flex-direction: column; justify-content: space-between;">
            <div>
                <span style="font-size: 12px; color: #818cf8; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">
                    <?= e($item['category_name']) ?>
                </span>
                <h4 style="font-size: 16px; margin: 8px 0 12px; font-weight: 600;">
                    <?= e($item['name']) ?>
                </h4>
                <div style="font-size: 18px; font-weight: 700; color: #ffffff; margin-bottom: 8px;">
                    $<?= number_format($item['price'], 2) ?>
                </div>
                <div style="font-size: 13px; color: <?= $item['stock_quantity'] > 0 ? 'var(--success)' : 'var(--danger)' ?>; margin-bottom: 16px;">
                    <?= $item['stock_quantity'] > 0 ? 'In Stock (' . $item['stock_quantity'] . ')' : 'Out of Stock' ?>
                </div>
            </div>

            <div style="display: flex; gap: 8px;">
                <a href="<?= base_url('customer/product_details.php?id=' . $item['id']) ?>" class="btn btn-secondary btn-sm" style="flex: 1;">
                    Details
                </a>
                <?php if ($item['stock_quantity'] > 0): ?>
                    <form method="POST" action="<?= base_url('customer/add_to_cart.php') ?>" style="flex: 1;">
                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary btn-sm" style="width: 100%;">
                            + Add
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btn btn-disabled btn-sm" disabled style="flex: 1;">
                        Sold Out
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>


<?php include APP_ROOT . "/app/Views/customer/customer_footer.php"; ?>