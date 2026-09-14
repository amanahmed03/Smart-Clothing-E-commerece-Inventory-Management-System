<?php
require_once __DIR__ . '/public/bootstrap.php';
use App\Core\Auth;
use App\Models\Product;

$productModel = new Product();
$featuredProducts = $productModel->getFeatured(4);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Clothing E-Commerce Management System</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-gradient: radial-gradient(circle at 50% 10%, #1e1b4b 0%, #0f172a 60%, #020617 100%);
            --card-bg: rgba(30, 41, 59, 0.7);
            --card-border: rgba(255, 255, 255, 0.08);
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --accent-glow: rgba(99, 102, 241, 0.35);
            --success: #10b981;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-gradient);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 24px;
            position: relative;
            overflow-x: hidden;
        }
        .ambient-glow {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 350px;
            background: radial-gradient(circle, var(--accent-glow) 0%, rgba(99, 102, 241, 0) 70%);
            filter: blur(60px);
            z-index: 0;
            pointer-events: none;
        }
        .container {
            position: relative;
            z-index: 1;
            max-width: 720px;
            width: 100%;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 48px 40px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        .status-badge {
            display: inline-flex; align-items: center; gap: 8px; padding: 6px 16px; border-radius: 999px;
            background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.25); color: var(--success);
            font-size: 13px; font-weight: 500; margin-bottom: 24px;
        }
        .pulse-dot { width: 8px; height: 8px; background-color: var(--success); border-radius: 50%; display: inline-block; box-shadow: 0 0 10px var(--success); animation: pulse 2s infinite ease-in-out; }
        @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.3); opacity: 0.6; } }
        h1 { font-family: 'Outfit', sans-serif; font-size: 34px; font-weight: 700; line-height: 1.25; letter-spacing: -0.02em; margin-bottom: 16px; background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        p.subtitle { color: var(--text-muted); font-size: 16px; line-height: 1.6; margin-bottom: 36px; max-width: 520px; margin-left: auto; margin-right: auto; }
        .meta-box { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; padding: 20px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; margin-bottom: 32px; text-align: left; }
        .meta-item { display: flex; flex-direction: column; gap: 4px; }
        .meta-label { font-size: 12px; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; }
        .meta-value { font-size: 14px; font-weight: 600; color: #f1f5f9; font-family: Consolas, monospace; }
        .action-group { display: flex; justify-content: center; gap: 16px; flex-wrap: wrap; margin-bottom: 24px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 12px 24px; border-radius: 10px; font-size: 14px; font-weight: 600; text-decoration: none; transition: all 0.2s ease; cursor: pointer; }
        .btn-primary { background-color: var(--accent); color: #ffffff; box-shadow: 0 4px 14px var(--accent-glow); }
        .btn-primary:hover { background-color: var(--accent-hover); transform: translateY(-1px); }
        .btn-secondary { background-color: #334155; color: var(--text-main); }
        .btn-secondary:hover { background-color: #475569; }
        .module-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-top: 32px; text-align: left; }
        .module-card { padding: 20px; background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255, 255, 255, 0.05); border-radius: 12px; text-decoration: none; color: inherit; transition: all 0.2s ease; }
        .module-card:hover { transform: translateY(-2px); border-color: rgba(99, 102, 241, 0.3); }
        .module-card .icon { font-size: 28px; margin-bottom: 8px; }
        .module-card h4 { font-family: 'Outfit', sans-serif; font-size: 15px; margin-bottom: 4px; color: #fff; }
        .module-card p { font-size: 12px; color: var(--text-muted); }
        footer { margin-top: 32px; font-size: 13px; color: var(--text-muted); text-align: center; }
    </style>
</head>
<body>
    <div class="ambient-glow"></div>
    <main class="container">
        <h1>Smart Clothing E-Commerce Management System</h1>
        <p class="subtitle">Full-stack e-commerce platform with customer portal, admin panel, inventory management, and delivery tracking.</p>
        <div class="action-group">
            <a href="<?= base_url('login.php') ?>" class="btn btn-primary">Login / Register</a>
        </div>
        <div class="module-grid">
            <a href="<?= base_url('login.php') ?>" class="module-card">
                <div class="icon">👤</div>
                <h4>Customer Portal</h4>
                <p>Browse, cart, checkout & orders</p>
            </a>
            <a href="<?= base_url('login.php') ?>" class="module-card">
                <div class="icon">🛡️</div>
                <h4>Admin Panel</h4>
                <p>Users, products & categories</p>
            </a>
            <a href="<?= base_url('login.php') ?>" class="module-card">
                <div class="icon">📦</div>
                <h4>Inventory</h4>
                <p>Stock & product management</p>
            </a>
            <a href="<?= base_url('login.php') ?>" class="module-card">
                <div class="icon">🚚</div>
                <h4>Delivery</h4>
                <p>Order tracking & dispatch</p>
            </a>
        </div>
    </main>
</body>
</html>
