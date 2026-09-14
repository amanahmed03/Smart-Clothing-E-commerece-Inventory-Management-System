<?php include APP_ROOT . "/app/Views/customer/customer_header.php"; ?>

<div style="margin-bottom: 28px;">
    <h1 style="font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 6px;">
        My Orders
    </h1>
    <p style="color: var(--text-secondary); font-size: 15px;">
        Track your past and current orders.
    </p>
</div>

<?php if (empty($orders)): ?>
    <div class="card" style="text-align: center; padding: 56px 20px;">
        <div style="font-size: 48px; margin-bottom: 16px;">📦</div>
        <h2 style="font-family: 'Outfit', sans-serif; font-size: 22px; margin-bottom: 8px;">No orders yet</h2>
        <p style="color: var(--text-secondary); font-size: 14px; max-width: 440px; margin: 0 auto 24px;">
            You haven't placed any orders yet. Start shopping to find smart apparel you love!
        </p>
        <a href="<?= base_url('customer/products.php') ?>" class="btn btn-primary" style="padding: 12px 28px;">
            Start Shopping
        </a>
    </div>
<?php else: ?>
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <?php foreach ($orders as $o): ?>
            <div class="card" style="padding: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                    <div>
                        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; color: #fff;">
                            Order #<?= e($o['order_number']) ?>
                        </h3>
                        <span style="font-size: 12px; color: var(--text-muted);">
                            Placed: <?= date('M j, Y h:i A', strtotime($o['created_at'])) ?>
                        </span>
                    </div>
                    <div style="display: flex; gap: 12px; align-items: center;">
                        <span class="badge badge-<?= str_replace('_', '', $o['status']) ?>">
                            <?= e($o['status']) ?>
                        </span>
                        <span style="font-size: 20px; font-weight: 700; color: #fff;">
                            $<?= number_format($o['total_amount'], 2) ?>
                        </span>
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 16px;">
                    <div style="font-size: 13px; color: var(--text-secondary);">
                        <strong>Delivery:</strong>
                        <span style="color: #fff; display: block; margin-top: 4px;">
                            <?= e($o['delivery_status'] ?? 'Not assigned') ?>
                        </span>
                    </div>
                    <div style="font-size: 13px; color: var(--text-secondary);">
                        <strong>Tracking:</strong>
                        <span style="color: #fff; display: block; margin-top: 4px; font-family: Consolas, monospace;">
                            <?= e($o['tracking_number'] ?? '—') ?>
                        </span>
                    </div>
                </div>
                <div style="display: flex; gap: 12px;">
                    <a href="<?= base_url('customer/order_detail.php?id=' . $o['order_id']) ?>" class="btn btn-secondary btn-sm">
                        View Details
                    </a>
                    <?php if ($o['status'] === 'pending'): ?>
                        <a href="<?= base_url('customer/checkout.php') ?>" class="btn btn-primary btn-sm">
                            Checkout
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>


<?php include APP_ROOT . "/app/Views/customer/customer_footer.php"; ?>