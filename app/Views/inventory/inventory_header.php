<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? e($pageTitle) . ' - ' : '' ?>Inventory Portal - Smart Clothing</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-dark: #0b1120;
            --card-bg: #1e293b;
            --card-hover: #243248;
            --border-color: #334155;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #f59e0b;
            --accent-hover: #d97706;
            --accent-light: rgba(245, 158, 11, 0.15);
            --success: #10b981;
            --success-bg: rgba(16, 185, 129, 0.12);
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --warning: #f59e0b;
            --warning-bg: rgba(245, 158, 11, 0.12);
            --info: #38bdf8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif; background-color: var(--bg-dark); color: var(--text-main); min-height: 100vh; display: flex; flex-direction: column; }
        .inv-navbar { background-color: rgba(30, 41, 59, 0.95); border-bottom: 1px solid var(--border-color); position: sticky; top: 0; z-index: 100; }
        .inv-nav-container { max-width: 1200px; margin: 0 auto; padding: 0 24px; height: 68px; display: flex; align-items: center; justify-content: space-between; }
        .inv-brand { font-family: 'Outfit', sans-serif; font-size: 20px; font-weight: 700; color: #ffffff; text-decoration: none; display: flex; align-items: center; gap: 8px; }
        .inv-brand span { color: var(--accent); }
        .inv-nav-links { display: flex; align-items: center; gap: 10px; list-style: none; }
        .inv-nav-link { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: 8px; color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 500; transition: all 0.2s ease; }
        .inv-nav-link:hover { color: var(--text-main); background-color: rgba(255, 255, 255, 0.05); }
        .inv-nav-link.active { color: #ffffff; background-color: var(--accent-light); border: 1px solid rgba(245, 158, 11, 0.3); }
        .inv-user-info { display: flex; align-items: center; gap: 12px; padding-left: 16px; border-left: 1px solid var(--border-color); }
        .inv-main { max-width: 1200px; margin: 0 auto; padding: 32px 24px 60px; width: 100%; flex: 1; }
        .page-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
        .page-title { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 700; color: #ffffff; }
        .page-subtitle { color: var(--text-muted); font-size: 14px; margin-top: 4px; }
        .alert { padding: 14px 18px; border-radius: 10px; margin-bottom: 24px; font-size: 14px; line-height: 1.4; display: flex; align-items: center; justify-content: space-between; }
        .alert-success { background-color: var(--success-bg); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; }
        .alert-danger { background-color: var(--danger-bg); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 32px; }
        .stat-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 22px; display: flex; align-items: center; justify-content: space-between; transition: transform 0.15s ease; }
        .stat-card:hover { transform: translateY(-2px); background: var(--card-hover); }
        .stat-title { font-size: 13px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; margin-bottom: 6px; }
        .stat-value { font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; color: #ffffff; }
        .stat-icon { font-size: 32px; opacity: 0.8; }
        .table-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; }
        .data-table { width: 100%; border-collapse: collapse; text-align: left; font-size: 14px; }
        .data-table thead { background: rgba(15, 23, 42, 0.6); border-bottom: 1px solid var(--border-color); }
        .data-table th { padding: 14px 18px; color: var(--text-muted); font-weight: 600; font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em; }
        .data-table td { padding: 14px 18px; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
        .data-table tbody tr:last-child td { border-bottom: none; }
        .data-table tbody tr:hover { background-color: rgba(255, 255, 255, 0.02); }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .badge-active { background: rgba(16, 185, 129, 0.15); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge-inactive { background: rgba(148, 163, 184, 0.15); color: var(--text-muted); border: 1px solid rgba(148, 163, 184, 0.3); }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 9px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; cursor: pointer; border: none; transition: all 0.15s ease; }
        .btn-primary { background-color: var(--accent); color: #ffffff; }
        .btn-primary:hover { background-color: var(--accent-hover); }
        .btn-secondary { background-color: #334155; color: var(--text-main); }
        .btn-secondary:hover { background-color: #475569; }
        .btn-danger { background-color: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #fca5a5; }
        .btn-danger:hover { background-color: #ef4444; color: #ffffff; }
        .btn-sm { padding: 5px 10px; font-size: 12px; border-radius: 6px; }
        .form-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 12px; padding: 28px; max-width: 640px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 6px; }
        .form-control { width: 100%; padding: 11px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #ffffff; font-size: 14px; outline: none; transition: border-color 0.2s; }
        .form-control:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.25); }
        .form-control:disabled, .form-control[readonly] { background: rgba(15, 23, 42, 0.5); color: var(--text-muted); cursor: not-allowed; }
        textarea.form-control { resize: vertical; }
        .form-help { font-size: 12px; color: var(--text-muted); margin-top: 4px; }
        .inv-footer { border-top: 1px solid var(--border-color); padding: 24px; text-align: center; color: var(--text-muted); font-size: 13px; background: rgba(15, 23, 42, 0.4); }
    </style>
</head>
<body>
<?php
$invUser = \App\Core\Auth::currentUser();
if (!isset($currentPage)) {
    $currentPage = basename($_SERVER['PHP_SELF'] ?? '');
}
?>
<header class="inv-navbar">
    <div class="inv-nav-container">
        <a href="<?= base_url('inventory/dashboard.php') ?>" class="inv-brand">
            Smart<span>Clothing</span> &bull; <small style="font-size: 12px; color: var(--warning); text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;">Inventory</small>
        </a>
        <nav>
            <ul class="inv-nav-links">
                <li>
                    <a href="<?= base_url('inventory/dashboard.php') ?>" class="inv-nav-link <?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= base_url('inventory/products.php') ?>" class="inv-nav-link <?= in_array($currentPage, ['products.php', 'product_edit.php', 'stock_update.php']) ? 'active' : '' ?>">
                        Products & Stock
                    </a>
                </li>
                <li class="inv-user-info">
                    <span style="font-size: 13px; font-weight: 600; color: #cbd5e1;"><?= e($invUser['name']) ?></span>
                    <a href="<?= base_url('logout.php') ?>" class="btn btn-danger btn-sm">
                        Logout
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</header>

<main class="inv-main">
    <?php if ($flashSuccess = get_flash('success')): ?>
        <div class="alert alert-success">
            <span><?= e($flashSuccess) ?></span>
        </div>
    <?php endif; ?>

    <?php if ($flashError = get_flash('error')): ?>
        <div class="alert alert-danger">
            <span><?= e($flashError) ?></span>
        </div>
    <?php endif; ?>