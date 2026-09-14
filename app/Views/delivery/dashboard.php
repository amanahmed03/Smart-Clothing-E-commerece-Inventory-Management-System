<?php include APP_ROOT . "/app/Views/delivery/delivery_header.php"; ?>

<?php
$pageTitle = 'Delivery Dashboard';

$totalOrdersStmt = $pdo->query("SELECT COUNT(*) AS total FROM orders");
$totalOrders = (int)$totalOrdersStmt->fetchColumn();

$pendingAssignmentStmt = $pdo->query("SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status = 'pending_assignment'");
$pendingAssignment = (int)$pendingAssignmentStmt->fetchColumn();

$outForDeliveryStmt = $pdo->query("SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status = 'out_for_delivery'");
$outForDelivery = (int)$outForDeliveryStmt->fetchColumn();

$deliveredTodayStmt = $pdo->query("SELECT COUNT(*) AS total FROM deliveries WHERE delivery_status = 'delivered' AND DATE(delivered_at) = CURDATE()");
$deliveredToday = (int)$deliveredTodayStmt->fetchColumn();

$recentDeliveriesStmt = $pdo->query("
    SELECT d.id, d.order_id, d.delivery_status, d.tracking_number, d.estimated_delivery, d.delivered_at,
           o.order_number, o.total_amount, u.name AS customer_name
    FROM deliveries d
    JOIN orders o ON d.order_id = o.id
    JOIN users u ON o.user_id = u.id
    ORDER BY d.created_at DESC
    LIMIT 10
");
$recentDeliveries = $recentDeliveriesStmt->fetchAll();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Delivery Dashboard</h1>
        <p class="page-subtitle">Overview of delivery operations and recent assignments.</p>
    </div>
</div>

<!-- Stats Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div>
            <div class="stat-title">Total Orders</div>
            <div class="stat-value"><?= $totalOrders ?></div>
        </div>
        <div class="stat-icon">📦</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Pending Assignment</div>
            <div class="stat-value" style="color: var(--warning);"><?= $pendingAssignment ?></div>
        </div>
        <div class="stat-icon">⏳</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Out for Delivery</div>
            <div class="stat-value" style="color: var(--info);"><?= $outForDelivery ?></div>
        </div>
        <div class="stat-icon">🚚</div>
    </div>
    <div class="stat-card">
        <div>
            <div class="stat-title">Delivered Today</div>
            <div class="stat-value" style="color: var(--success);"><?= $deliveredToday ?></div>
        </div>
        <div class="stat-icon">✅</div>
    </div>
</div>

<!-- Quick Actions -->
<div style="margin-bottom: 24px;">
    <a href="<?= base_url('delivery/orders.php') ?>" class="btn btn-primary" style="padding: 12px 28px;">
        📦 View All Orders &rarr;
    </a>
</div>

<!-- Recent Deliveries -->
<div class="table-card">
    <div style="padding: 18px 20px; border-bottom: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
        <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px;">Recent Deliveries</h3>
        <a href="<?= base_url('delivery/orders.php') ?>" style="color: var(--accent); font-size: 13px; text-decoration: none; font-weight: 600;">
            View All &rarr;
        </a>
    </div>
    <?php if (empty($recentDeliveries)): ?>
        <div style="text-align: center; padding: 48px 20px; color: var(--text-muted);">
            <div style="font-size: 40px; margin-bottom: 12px;">🚚</div>
            <p>No deliveries assigned yet.</p>
        </div>
    <?php else: ?>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Tracking</th>
                    <th>Est. Delivery</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentDeliveries as $d): ?>
                    <tr>
                        <td><strong style="color: #fff;"><?= e($d['order_number']) ?></strong></td>
                        <td><?= e($d['customer_name']) ?></td>
                        <td>$<?= number_format($d['total_amount'], 2) ?></td>
                        <td><span class="badge badge-<?= str_replace('_', '', $d['delivery_status']) ?>"><?= e($d['delivery_status']) ?></span></td>
                        <td style="font-family: Consolas, monospace; font-size: 12px;"><?= e($d['tracking_number'] ?? '—') ?></td>
                        <td><?= $d['estimated_delivery'] ? date('M j, Y', strtotime($d['estimated_delivery'])) : '—' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include APP_ROOT . "/app/Views/delivery/delivery_footer.php"; ?>