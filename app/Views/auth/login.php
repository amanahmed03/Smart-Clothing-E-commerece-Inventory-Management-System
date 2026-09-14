<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Smart Clothing E-Commerce</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #0b1120;
            --card-bg: rgba(30, 41, 59, 0.85);
            --border: #334155;
            --text-primary: #f8fafc;
            --text-secondary: #94a3b8;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --danger: #ef4444;
            --danger-bg: rgba(239, 68, 68, 0.12);
            --danger-border: rgba(239, 68, 68, 0.3);
            --success: #10b981;
            --success-bg: rgba(16, 185, 129, 0.12);
            --success-border: rgba(16, 185, 129, 0.3);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
            background: radial-gradient(circle at top, #1e1b4b 0%, #0f172a 50%, var(--bg) 100%);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }
        .auth-container {
            width: 100%; max-width: 440px; background: var(--card-bg);
            border: 1px solid var(--border); border-radius: 16px;
            padding: 36px 32px; backdrop-filter: blur(12px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .auth-header { text-align: center; margin-bottom: 28px; }
        .brand-link {
            font-size: 13px; text-transform: uppercase; letter-spacing: 0.08em;
            color: #818cf8; text-decoration: none; font-weight: 600; display: inline-block; margin-bottom: 8px;
        }
        h1 { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 700; color: #ffffff; margin-bottom: 6px; }
        .subtitle { color: var(--text-secondary); font-size: 14px; }
        .alert { padding: 12px 16px; border-radius: 8px; font-size: 14px; margin-bottom: 20px; line-height: 1.4; }
        .alert-error { background-color: var(--danger-bg); border: 1px solid var(--danger-border); color: #fca5a5; }
        .alert-success { background-color: var(--success-bg); border: 1px solid var(--success-border); color: #86efac; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: 13px; font-weight: 500; color: #cbd5e1; margin-bottom: 6px; }
        input[type="email"], input[type="password"] {
            width: 100%; padding: 12px 14px; border-radius: 8px; background: #0f172a;
            border: 1px solid var(--border); color: var(--text-primary); font-size: 14px; outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        input[type="email"]:focus, input[type="password"]:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.25); }
        .btn-submit {
            width: 100%; padding: 12px; border: none; border-radius: 8px;
            background-color: var(--accent); color: white; font-size: 15px; font-weight: 600;
            cursor: pointer; transition: background-color 0.2s, transform 0.1s; margin-top: 8px;
        }
        .btn-submit:hover { background-color: var(--accent-hover); }
        .btn-submit:active { transform: scale(0.99); }
        .auth-footer { margin-top: 24px; text-align: center; font-size: 14px; color: var(--text-secondary); }
        .auth-footer a { color: #a5b4fc; text-decoration: none; font-weight: 500; }
        .auth-footer a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <a href="<?= base_url('index.php') ?>" class="brand-link">Smart Clothing</a>
            <h1>Welcome Back</h1>
            <p class="subtitle">Log in to manage your account and orders</p>
        </div>
        <?php if (!empty($flashSuccess)): ?><div class="alert alert-success"><?= e($flashSuccess) ?></div><?php endif; ?>
        <?php if (!empty($flashError)): ?><div class="alert alert-error"><?= e($flashError) ?></div><?php endif; ?>
        <?php if (!empty($error)): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>
        <form method="POST" action="<?= base_url('login.php') ?>" novalidate>
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-submit">Sign In</button>
        </form>
        <div class="auth-footer">
            Don't have an account? <a href="<?= base_url('register.php') ?>">Register here</a>
        </div>
    </div>
</body>
</html>
