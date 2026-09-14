<?php include APP_ROOT . "/app/Views/admin/admin_header.php"; ?>

<?php if (!empty($flashSuccess)): ?>
<div class="alert alert-success"><span><?= e($flashSuccess) ?></span></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="alert alert-danger"><span><?= e($flashError) ?></span></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle">View, edit roles, and manage system accounts across all user types.</p>
    </div>
</div>

<div class="table-card">
    <div style="overflow-x: auto;">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>User / Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Phone</th>
                    <th>Registered Date</th>
                    <th style="text-align: right; width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 32px; color: var(--text-muted);">
                            No users found in database.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <?php 
                            $isSelf = ((int)$u['id'] === (int)$adminUser['id']);
                            $badgeClass = 'badge-customer';
                            if ($u['role'] === 'admin') $badgeClass = 'badge-admin';
                            elseif ($u['role'] === 'inventory_manager') $badgeClass = 'badge-inventory';
                            elseif ($u['role'] === 'delivery_manager') $badgeClass = 'badge-delivery';
                        ?>
                        <tr>
                            <td>#<?= (int)$u['id'] ?></td>
                            <td>
                                <strong style="color: #fff;"><?= e($u['name']) ?></strong>
                                <?php if ($isSelf): ?>
                                    <span style="font-size: 11px; background: rgba(99,102,241,0.2); color: #a5b4fc; padding: 2px 6px; border-radius: 4px; margin-left: 6px;">You</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <code><?= e($u['email']) ?></code>
                            </td>
                            <td>
                                <span class="badge <?= $badgeClass ?>">
                                    <?= e($u['role']) ?>
                                </span>
                            </td>
                            <td style="color: var(--text-muted); font-size: 13px;">
                                <?= e($u['phone'] ?: '—') ?>
                            </td>
                            <td style="color: var(--text-muted); font-size: 13px; white-space: nowrap;">
                                <?= date('M j, Y', strtotime($u['created_at'])) ?>
                            </td>
                            <td style="text-align: right; white-space: nowrap;">
                                <a href="<?= base_url('admin/user_edit.php?id=' . $u['id']) ?>" class="btn btn-secondary btn-sm">
                                    Edit
                                </a>

                                <?php if ($isSelf): ?>
                                    <button class="btn btn-disabled btn-sm" disabled title="You cannot delete your own active account">
                                        Delete
                                    </button>
                                <?php else: ?>
                                    <a 
                                        href="<?= base_url('admin/user_delete.php?id=' . $u['id']) ?>" 
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('Are you sure you want to delete user \'<?= htmlspecialchars(addslashes($u['name']), ENT_QUOTES) ?>\'? This action cannot be undone.');"
                                    >
                                        Delete
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


<?php include APP_ROOT . "/app/Views/admin/admin_footer.php"; ?>