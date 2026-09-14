<?php include APP_ROOT . "/app/Views/delivery/delivery_header.php"; ?>

<?php
$orderId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);

if (!$orderId || $orderId <= 0) {
    set_flash('error', 'Invalid order ID.');
    redirect(base_url('delivery/orders.php'));
}

// Fetch order details
$orderStmt = $pdo->prepare("
    SELECT o.*, u.name AS customer_name, u.email AS customer_email, u.phone AS customer_phone, u.address AS customer_address
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = :id
    LIMIT 1
");
$orderStmt->execute([':id' => $orderId]);
$order = $orderStmt->fetch();

if (!$order) {
    set_flash('error', 'Order not found.');
    redirect(base_url('delivery/orders.php'));
}

// Fetch order items
$itemsStmt = $pdo->prepare("
    SELECT oi.*, p.name AS product_name, p.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = :order_id
");
$itemsStmt->execute([':order_id' => $orderId]);
$orderItems = $itemsStmt->fetchAll();

// Fetch delivery info
$deliveryStmt = $pdo->prepare("SELECT * FROM deliveries WHERE order_id = :id LIMIT 1");
$deliveryStmt->execute([':id' => $orderId]);
$delivery = $deliveryStmt->fetch();

// Fetch delivery managers for assignment
$managerStmt = $pdo->query("SELECT id, name FROM users WHERE role = 'delivery_manager' ORDER BY name ASC");
$deliveryManagers = $managerStmt->fetchAll();

$errors = [];
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Order #<?= e($order['order_number']) ?></h1>
        <p class="page-subtitle">Order placed on <?= date('M j, Y', strtotime($order['created_at'])) ?> &bull; Status: <span class="badge badge-<?= str_replace('_', '', $order['status']) ?>"><?= e($order['status']) ?></span></p>
    </div>
    <a href="<?= base_url('delivery/orders.php') ?>" class="btn btn-secondary">
        &larr; Back to Orders
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

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px;">
    <!-- Customer Info -->
    <div class="table-card" style="padding: 24px;">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            Customer Info
        </h3>
        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 14px;">
            <div><strong style="color: var(--text-muted);">Name:</strong> <span style="color: #fff;"><?= e($order['customer_name']) ?></span></div>
            <div><strong style="color: var(--text-muted);">Email:</strong> <span style="color: #fff;"><?= e($order['customer_email']) ?></span></div>
            <div><strong style="color: var(--text-muted);">Phone:</strong> <span style="color: #fff;"><?= e($order['customer_phone'] ?: '—') ?></span></div>
            <div><strong style="color: var(--text-muted);">Address:</strong> <span style="color: #fff;"><?= e($order['customer_address'] ?: $order['shipping_address']) ?></span></div>
        </div>
    </div>

    <!-- Order Summary -->
    <div class="table-card" style="padding: 24px;">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            Order Summary
        </h3>
        <div style="display: flex; flex-direction: column; gap: 10px; font-size: 14px;">
            <div><strong style="color: var(--text-muted);">Order #:</strong> <span style="color: #fff;"><?= e($order['order_number']) ?></span></div>
            <div><strong style="color: var(--text-muted);">Total Amount:</strong> <span style="color: #fff; font-size: 18px; font-weight: 700;">$<?= number_format($order['total_amount'], 2) ?></span></div>
            <div><strong style="color: var(--text-muted);">Shipping Phone:</strong> <span style="color: #fff;"><?= e($order['shipping_phone']) ?></span></div>
            <div><strong style="color: var(--text-muted);">Notes:</strong> <span style="color: #fff;"><?= e($order['notes'] ?: 'None') ?></span></div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="table-card" style="margin-bottom: 24px;">
    <div style="padding: 18px 20px; border-bottom: 1px solid var(--border-color);">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px;">Order Items (<?= count($orderItems) ?>)</h3>
    </div>
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Size</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orderItems as $item): ?>
                    <tr>
                        <td>
                            <strong style="color: #fff;"><?= e($item['product_name']) ?></strong>
                        </td>
                        <td>$<?= number_format($item['price'], 2) ?></td>
                        <td><?= (int)$item['quantity'] ?></td>
                        <td><?= e($item['selected_size'] ?? 'M') ?></td>
                        <td>$<?= number_format($item['price'] * (int)$item['quantity'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delivery Assignment & Status -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
    <!-- Assign Delivery -->
    <div class="form-card">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            🚚 Assign Delivery Manager
        </h3>
        <form method="POST" action="order_detail.php?id=<?= $orderId ?>">
            <div class="form-group">
                <label for="delivery_manager_id" class="form-label">Delivery Manager</label>
                <select id="delivery_manager_id" name="delivery_manager_id" class="form-control">
                    <option value="">-- Select Manager --</option>
                    <?php foreach ($deliveryManagers as $dm): ?>
                        <option value="<?= $dm['id'] ?>" <?= ($delivery && (int)$delivery['delivery_manager_id'] === (int)$dm['id']) ? 'selected' : '' ?>>
                            <?= e($dm['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tracking_number" class="form-label">Tracking Number</label>
                <input type="text" id="tracking_number" name="tracking_number" class="form-control" value="<?= e($delivery['tracking_number'] ?? '') ?>" placeholder="e.g. TRK-001234">
            </div>
            <div class="form-group">
                <label for="estimated_delivery" class="form-label">Estimated Delivery Date</label>
                <input type="date" id="estimated_delivery" name="estimated_delivery" class="form-control" value="<?= $delivery['estimated_delivery'] ?? '' ?>">
            </div>
            <button type="submit" name="assign_delivery" class="btn btn-primary" style="width: 100%;">Assign Delivery</button>
        </form>
    </div>

    <!-- Update Delivery Status -->
    <div class="form-card">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; border-bottom: 1px solid var(--border-color); padding-bottom: 12px;">
            📦 Update Delivery Status
        </h3>
        <form method="POST" action="order_detail.php?id=<?= $orderId ?>">
            <div class="form-group">
                <label for="delivery_status" class="form-label">Current Status</label>
                <select id="delivery_status" name="delivery_status" class="form-control">
                    <option value="pending_assignment" <?= ($delivery['delivery_status'] ?? '') === 'pending_assignment' ? 'selected' : '' ?>>Pending Assignment</option>
                    <option value="assigned" <?= ($delivery['delivery_status'] ?? '') === 'assigned' ? 'selected' : '' ?>>Assigned</option>
                    <option value="out_for_delivery" <?= ($delivery['delivery_status'] ?? '') === 'out_for_delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                    <option value="delivered" <?= ($delivery['delivery_status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    <option value="returned" <?= ($delivery['delivery_status'] ?? '') === 'returned' ? 'selected' : '' ?>>Returned</option>
                </select>
            </div>
            <button type="submit" name="update_status" class="btn btn-primary" style="width: 100%;">Update Status</button>
        </form>
    </div>
</div>

<?php include APP_ROOT . "/app/Views/delivery/delivery_footer.php"; ?>