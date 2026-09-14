<?php include APP_ROOT . "/app/Views/admin/admin_header.php"; ?>

<?php if (!empty($flashSuccess)): ?>
<div class="alert alert-success"><span><?= e($flashSuccess) ?></span></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="alert alert-danger"><span><?= e($flashError) ?></span></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit User Account</h1>
        <p class="page-subtitle">Update identity and role permissions for user ID #<?= (int)$userId ?>.</p>
    </div>
    <a href="<?= base_url('admin/users.php') ?>" class="btn btn-secondary">
        &larr; Back to Users
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

<div class="form-card">
    <form method="POST" action="user_edit.php?id=<?= $userId ?>" novalidate>
        <!-- Email Display (Read-only) -->
        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-control" value="<?= e($targetUser['email']) ?>" readonly>
            <div class="form-help">User emails cannot be modified to prevent identity conflicts.</div>
        </div>

        <!-- Name Field -->
        <div class="form-group">
            <label for="name" class="form-label">Full Name <span style="color: var(--danger);">*</span></label>
            <input 
                type="text" 
                id="name" 
                name="name" 
                class="form-control" 
                value="<?= e($name ?? $targetUser['name']) ?>" 
                required
            >
        </div>

        <!-- Role Selector -->
        <div class="form-group">
            <label for="role" class="form-label">System Role <span style="color: var(--danger);">*</span></label>
            <select id="role" name="role" class="form-control" required>
                <?php foreach ($validRoles as $r): ?>
                    <?php 
                        $roleLabel = ucwords(str_replace('_', ' ', $r));
                        $isSelected = (($role ?? $targetUser['role']) === $r);
                    ?>
                    <option value="<?= $r ?>" <?= $isSelected ? 'selected' : '' ?>><?= $roleLabel ?> (<?= $r ?>)</option>
                <?php endforeach; ?>
            </select>
            <div class="form-help">Assigns authorization levels and role-specific dashboard access.</div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 28px;">
            <button type="submit" class="btn btn-primary">
                Save Changes
            </button>
            <a href="<?= base_url('admin/users.php') ?>" class="btn btn-secondary">
                Cancel
            </a>
        </div>
    </form>
</div>


<?php include APP_ROOT . "/app/Views/admin/admin_footer.php"; ?>