<?php include APP_ROOT . "/app/Views/customer/customer_header.php"; ?>

<div style="margin-bottom: 28px;">
    <h1 style="font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 700; margin-bottom: 6px;">
        Account Profile
    </h1>
    <p style="color: var(--text-secondary); font-size: 15px;">
        View your account credentials and update your personal contact details.
    </p>
</div>

<div style="max-width: 680px;">
    <?php if (!empty($errors)): ?>
        <div class="alert alert-error" style="margin-bottom: 24px;">
            <div>
                <?php foreach ($errors as $err): ?>
                    <div>&bull; <?= e($err) ?></div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="card" style="padding: 32px;">
        <form method="POST" action="profile.php" novalidate>
            <!-- Name Field -->
            <div style="margin-bottom: 20px;">
                <label for="name" style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px;">
                    Full Name <span style="color: var(--danger);">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="<?= e($_POST['name'] ?? $user['name']) ?>" 
                    required 
                    style="width: 100%; padding: 12px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #ffffff; font-size: 14px;"
                >
            </div>

            <!-- Email Field (Read-only / Unchangeable) -->
            <div style="margin-bottom: 20px;">
                <label for="email" style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px;">
                    Email Address <span style="font-size: 12px; color: var(--text-secondary); font-weight: 400;">(Cannot be modified)</span>
                </label>
                <input 
                    type="email" 
                    id="email" 
                    value="<?= e($user['email']) ?>" 
                    disabled 
                    style="width: 100%; padding: 12px 14px; background: rgba(15, 23, 42, 0.5); border: 1px solid #1e293b; border-radius: 8px; color: var(--text-secondary); font-size: 14px; cursor: not-allowed;"
                >
            </div>

            <!-- Role Badge Display -->
            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px;">
                    Account Role
                </label>
                <div>
<span style="display: inline-block; padding: 6px 14px; border-radius: 999px; background: rgba(16, 185, 129, 0.15); color: var(--success); font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border: 1px solid rgba(16, 185, 129, 0.3);">
                            <?= $user['role'] === 'customer' ? 'Customer' : ($user['role'] === 'admin' ? 'Admin' : $user['role']) ?>
                        </span>
                </div>
            </div>

            <!-- Phone Field -->
            <div style="margin-bottom: 20px;">
                <label for="phone" style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px;">
                    Phone Number
                </label>
                <input 
                    type="text" 
                    id="phone" 
                    name="phone" 
                    value="<?= e($_POST['phone'] ?? $user['phone']) ?>" 
                    placeholder="e.g. +1-555-0199" 
                    style="width: 100%; padding: 12px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #ffffff; font-size: 14px;"
                >
            </div>

            <!-- Address Field -->
            <div style="margin-bottom: 28px;">
                <label for="address" style="display: block; font-size: 13px; font-weight: 600; color: #cbd5e1; margin-bottom: 8px;">
                    Delivery / Shipping Address
                </label>
                <textarea 
                    id="address" 
                    name="address" 
                    rows="3" 
                    placeholder="e.g. 123 Smart Ave, Tech City" 
                    style="width: 100%; padding: 12px 14px; background: #0f172a; border: 1px solid var(--border-color); border-radius: 8px; color: #ffffff; font-size: 14px; resize: vertical;"
                ><?= e($_POST['address'] ?? $user['address']) ?></textarea>
            </div>

            <div style="display: flex; gap: 14px; align-items: center;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 28px;">
                    Save Profile Changes
                </button>
                <a href="<?= base_url('customer/dashboard.php') ?>" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>

        <div style="margin-top: 32px; border-top: 1px solid var(--border-color); padding-top: 16px; font-size: 12px; color: var(--text-secondary);">
            Account registered on: <?= date('F j, Y', strtotime($user['created_at'])) ?>
        </div>
    </div>
</div>


<?php include APP_ROOT . "/app/Views/customer/customer_footer.php"; ?>