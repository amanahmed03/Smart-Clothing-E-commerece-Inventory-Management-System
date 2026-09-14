<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\Category;

class CategoryController extends Controller
{
    public function categories(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $categories = (new Category())->getAll();
        $data = ['pageTitle' => 'Category Management', 'adminUser' => $adminUser, 'categories' => $categories];
        include APP_ROOT . '/app/Views/admin/categories.php';
        exit;
    }

    public function categoryAdd(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $errors = []; $name = ''; $description = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? ''); $description = trim($_POST['description'] ?? '');
            if (empty($name)) { $errors[] = 'Category Name is required.'; } elseif (strlen($name) < 2 || strlen($name) > 100) { $errors[] = 'Category Name must be between 2 and 100 characters.'; }
            if (empty($errors)) { try { (new Category())->create($name, $description); set_flash('success', 'Category "' . $name . '" created successfully.'); $this->redirect(Auth::baseUrl() . 'admin/categories.php'); } catch (\PDOException $e) { $errors[] = 'Database error: Could not create category.'; } }
        }
        $data = ['pageTitle' => 'Add New Category', 'adminUser' => $adminUser, 'errors' => $errors, 'name' => $name, 'description' => $description];
        include APP_ROOT . '/app/Views/admin/category_add.php';
        exit;
    }

    public function categoryEdit(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $catId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$catId || $catId <= 0) { set_flash('error', 'Invalid category ID.'); $this->redirect(Auth::baseUrl() . 'admin/categories.php'); }
        $categoryModel = new Category();
        $category = $categoryModel->findById($catId);
        if (!$category) { set_flash('error', 'Category not found.'); $this->redirect(Auth::baseUrl() . 'admin/categories.php'); }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? ''); $description = trim($_POST['description'] ?? '');
            if (empty($name)) { $errors[] = 'Category Name is required.'; } elseif (strlen($name) < 2 || strlen($name) > 100) { $errors[] = 'Category Name must be between 2 and 100 characters.'; }
            if (empty($errors)) { try { $categoryModel->update($catId, $name, $description); set_flash('success', 'Category "' . $name . '" updated successfully.'); $this->redirect(Auth::baseUrl() . 'admin/categories.php'); } catch (\PDOException $e) { $errors[] = 'Database error: Could not update category.'; } }
        }
        $data = ['pageTitle' => 'Edit Category #' . $category['id'], 'adminUser' => $adminUser, 'category' => $category, 'errors' => $errors];
        include APP_ROOT . '/app/Views/admin/category_edit.php';
        exit;
    }

    public function categoryDelete(): void
    {
        Auth::require_role('admin');
        $catId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$catId || $catId <= 0) { set_flash('error', 'Invalid category ID.'); $this->redirect(Auth::baseUrl() . 'admin/categories.php'); }
        try {
            $categoryModel = new Category();
            $category = $categoryModel->findById($catId);
            if (!$category) { set_flash('error', 'Category not found.'); } else { $categoryModel->delete($catId); set_flash('success', 'Category "' . $category['name'] . '" deleted successfully.'); }
        } catch (\PDOException $e) { set_flash('error', 'Cannot delete category: This category may be linked to products.'); }
        $this->redirect(Auth::baseUrl() . 'admin/categories.php');
    }
}
