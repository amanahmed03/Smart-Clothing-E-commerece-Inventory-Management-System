<?php include APP_ROOT . "/app/Views/customer/customer_header.php"; ?>

<div style="margin-bottom: 28px;">
    <a href="<?= base_url('customer/orders.php') ?>" style="color: var(--text-secondary); text-decoration: none; font-size: 14px;">
        &larr; Back to Orders
    </a>
    <h1 style="font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 6px; margin-top: 8px;">
        Order #<?= e($order['order_number']) ?>
    </h1>
    <p style="color: var(--text-secondary); font-size: 15px;">
        Placed on <?= date('F j, Y \a\t g:i A', strtotime($order['created_at'])) ?>
    </p>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 32px;">
    <!-- Order Details -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Status -->
        <div class="card" style="padding: 24px;">
            <div style="display: flex; align-items: center; gap: 16px; flex-wrap: wrap;">
                <span class="badge badge-<?= str_replace('_', '', $order['status']) ?>" style="font-size: 14px; padding: 6px 16px;">
                    <?= e($order['status']) ?>
                </span>
                <span style="font-size: 14px; color: var(--text-secondary);">
                    Delivery: <?= e($delivery['delivery_status'] ?? 'Not assigned') ?>
                </span>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card" style="padding: 24px;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                Items (<?= count($orderItems) ?>)
            </h3>
            <?php foreach ($orderItems as $item): ?>
                <div style="display: flex; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--border-color);">
                    <div>
                        <strong style="color: #fff;"><?= e($item['product_name']) ?></strong>
                        <div style="font-size: 12px; color: var(--text-muted);">Size: <?= e($item['selected_size']) ?></div>
                    </div>
                    <div style="text-align: right;">
                        <span style="color: #fff;">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        <div style="font-size: 12px; color: var(--text-muted);">x<?= $item['quantity'] ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Shipping & Payment -->
        <div class="card" style="padding: 24px;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                Shipping & Payment
            </h3>
            <div style="display: flex; flex-direction: column; gap: 12px; font-size: 14px;">
                <div><strong style="color: var(--text-muted);">Shipping Address:</strong><br><span style="color: #fff;"><?= nl2br(e($order['shipping_address'])) ?></span></div>
                <div><strong style="color: var(--text-muted);">Phone:</strong> <span style="color: #fff;"><?= e($order['shipping_phone']) ?></span></div>
                <?php if ($order['notes']): ?>
                    <div><strong style="color: var(--text-muted);">Notes:</strong> <span style="color: #fff;"><?= e($order['notes']) ?></span></div>
                <?php endif; ?>
                <div><strong style="color: var(--text-muted);">Payment Method:</strong> <span style="color: #fff;"><?= e($payment['payment_method'] ?? '—') ?></span></div>
                <div><strong style="color: var(--text-muted);">Payment Status:</strong> <span style="color: #fff;"><?= e($payment['payment_status'] ?? '—') ?></span></div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div style="display: flex; flex-direction: column; gap: 24px;">
        <!-- Order Total -->
        <div class="card" style="padding: 24px;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                Order Total
            </h3>
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-secondary);">
                    <span>Items</span>
                    <span>$<?= number_format($order['total_amount'], 2) ?></span>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 14px; color: var(--text-secondary);">
                    <span>Shipping</span>
                    <span style="color: var(--success);">Free</span>
                </div>
                <div style="border-top: 1px solid var(--border-color); padding-top: 14px; display: flex; justify-content: space-between; align-items: baseline;">
                    <span style="font-size: 20px; font-weight: 700;">Total</span>
                    <span style="font-size: 28px; font-weight: 700; color: #fff;">$<?= number_format($order['total_amount'], 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Tracking -->
        <div class="card" style="padding: 24px;">
            <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
                Delivery Tracking
            </h3>
            <?php if ($delivery): ?>
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 14px;">
                    <div><strong>Tracking #:</strong> <span style="color: #fff; font-family: Consolas, monospace;"><?= e($delivery['tracking_number'] ?? '—') ?></span></div>
                    <div><strong>Status:</strong> <span style="color: #fff;"><?= e($delivery['delivery_status'] ?? '—') ?></span></div>
                    <?php if ($delivery['estimated_delivery']): ?>
                        <div><strong>Est. Delivery:</strong> <span style="color: #fff;"><?= date('M j, Y', strtotime($delivery['estimated_delivery'])) ?></span></div>
                    <?php endif; ?>
                    <?php if ($delivery['delivered_at']): ?>
                        <div><strong>Delivered At:</strong> <span style="color: #fff;"><?= date('M j, Y h:i A', strtotime($delivery['delivered_at'])) ?></span></div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 14px;">No delivery assignment yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>


<?php include APP_ROOT . "/app/Views/customer/customer_footer.php"; ?>