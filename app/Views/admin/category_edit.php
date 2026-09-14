<?php include APP_ROOT . "/app/Views/admin/admin_header.php"; ?>

<?php if (!empty($flashSuccess)): ?>
<div class="alert alert-success"><span><?= e($flashSuccess) ?></span></div>
<?php endif; ?>
<?php if (!empty($flashError)): ?>
<div class="alert alert-danger"><span><?= e($flashError) ?></span></div>
<?php endif; ?>

<div class="page-header">
    <div>
        <h1 class="page-title">Edit Category</h1>
        <p class="page-subtitle">Update "<?= e($category['name']) ?>".</p>
    </div>
    <a href="<?= base_url('admin/categories.php') ?>" class="btn btn-secondary">
        &larr; Back to Categories
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
    <form method="POST" action="category_edit.php?id=<?= $catId ?>" novalidate>
        <div class="form-group">
            <label for="name" class="form-label">Category Name <span style="color: var(--danger);">*</span></label>
            <input type="text" id="name" name="name" class="form-control" value="<?= e($category['name']) ?>" required autofocus>
        </div>

        <div class="form-group">
            <label for="slug" class="form-label">URL Slug</label>
            <input type="text" id="slug" name="slug" class="form-control" value="<?= e($category['slug']) ?>">
        </div>

        <div class="form-group">
            <label for="description" class="form-label">Description</label>
            <textarea id="description" name="description" rows="4" class="form-control"><?= e($category['description']) ?></textarea>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 28px;">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="<?= base_url('admin/categories.php') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>


<?php include APP_ROOT . "/app/Views/admin/admin_footer.php"; ?>