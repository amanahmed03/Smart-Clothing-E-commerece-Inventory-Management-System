<?php include APP_ROOT . "/app/Views/delivery/delivery_header.php"; ?>

<?php
$pageTitle = 'Delivery Orders';

// Fetch orders with user and delivery info
$filterStatus = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "
    SELECT o.id AS order_id, o.order_number, o.total_amount, o.status, o.shipping_address, o.shipping_phone,
           u.name AS customer_name, u.email AS customer_email,
           d.delivery_status, d.delivery_manager_id, d.tracking_number, d.estimated_delivery, d.delivered_at,
           d.id AS delivery_id
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN deliveries d ON o.id = d.order_id
";

$params = [];
if ($filterStatus) {
    $sql .= " WHERE o.status = :status";
    $params[':status'] = $filterStatus;
}

$sql .= " ORDER BY o.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

// Delivery managers for assignment
$managerStmt = $pdo->query("SELECT id, name FROM users WHERE role = 'delivery_manager' ORDER BY name ASC");
$deliveryManagers = $managerStmt->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Delivery Orders</h1>
        <p class="page-subtitle">Track and manage order deliveries.</p>
    </div>
</div>

<!-- Filter -->
<div class="card" style="padding: 16px 20px; margin-bottom: 24px;">
    <form method="GET" action="orders.php" style="display: flex; gap: 12px; align-items: center; flex-wrap: wrap;">
        <span style="font-size: 13px; color: var(--text-secondary); font-weight: 600;">Filter by Status:</span>
        <select name="status" class="form-control" style="width: auto; padding: 8px 12px;" onchange="this.form.submit()">
            <option value="">All Orders</option>
            <option value="pending" <?= $filterStatus === 'pending' ? 'selected' : '' ?>>Pending</option>
            <option value="processing" <?= $filterStatus === 'processing' ? 'selected' : '' ?>>Processing</option>
            <option value="shipped" <?= $filterStatus === 'shipped' ? 'selected' : '' ?>>Shipped</option>
            <option value="delivered" <?= $filterStatus === 'delivered' ? 'selected' : '' ?>>Delivered</option>
            <option value="cancelled" <?= $filterStatus === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
    </form>
</div>

<div class="table-card">
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Order Status</th>
                    <th>Delivery Status</th>
                    <th>Tracking</th>
                    <th>Est. Delivery</th>
                    <th>Assigned To</th>
                    <th style="text-align: right; width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No orders found.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $o): ?>
                        <tr>
                            <td>
                                <strong style="color: #fff;"><?= e($o['order_number']) ?></strong>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #fff;"><?= e($o['customer_name']) ?></div>
                                <div style="font-size: 12px; color: var(--text-muted);"><?= e($o['customer_email']) ?></div>
                            </td>
                            <td>$<?= number_format($o['total_amount'], 2) ?></td>
                            <td>
                                <span class="badge badge-<?= str_replace('_', '', $o['status']) ?>">
                                    <?= e($o['status']) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?= str_replace('_', '', $o['delivery_status'] ?? 'pending_assignment') ?>">
                                    <?= e($o['delivery_status'] ?? 'Not Assigned') ?>
                                </span>
                            </td>
                            <td style="font-family: Consolas, monospace; font-size: 12px;">
                                <?= e($o['tracking_number'] ?? '—') ?>
                            </td>
                            <td style="white-space: nowrap; font-size: 13px;">
                                <?= $o['estimated_delivery'] ? date('M j, Y', strtotime($o['estimated_delivery'])) : '—' ?>
                            </td>
                            <td>
                                <?php
                                $managerName = '';
                                if ($o['delivery_manager_id']) {
                                    foreach ($deliveryManagers as $dm) {
                                        if ((int)$dm['id'] === (int)$o['delivery_manager_id']) {
                                            $managerName = $dm['name'];
                                            break;
                                        }
                                    }
                                }
                                echo $managerName ? e($managerName) : '<span style="color: var(--text-muted);">Unassigned</span>';
                                ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= base_url('delivery/order_detail.php?id=' . $o['order_id']) ?>" class="btn btn-secondary btn-sm">
                                    View
                                </a>
                                <?php if (!$o['delivery_manager_id']): ?>
                                    <a href="<?= base_url('delivery/assign_delivery.php?id=' . $o['order_id']) ?>" class="btn btn-primary btn-sm">
                                        Assign
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include APP_ROOT . "/app/Views/delivery/delivery_footer.php"; ?>