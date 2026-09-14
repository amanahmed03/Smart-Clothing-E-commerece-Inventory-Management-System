<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class UserController extends Controller
{
    public function users(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $users = (new User())->getAll();
        $data = ['pageTitle' => 'User Management', 'adminUser' => $adminUser, 'users' => $users];
        include APP_ROOT . '/app/Views/admin/users.php';
        exit;
    }

    public function userEdit(): void
    {
        Auth::require_role('admin');
        $adminUser = Auth::currentUser();
        $userId = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$userId || $userId <= 0) { set_flash('error', 'Invalid user ID.'); $this->redirect(Auth::baseUrl() . 'admin/users.php'); }
        $userModel = new User();
        $targetUser = $userModel->findById($userId);
        if (!$targetUser) { set_flash('error', 'User not found.'); $this->redirect(Auth::baseUrl() . 'admin/users.php'); }
        $validRoles = ['admin', 'inventory_manager', 'delivery_manager', 'customer'];
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? ''); $role = trim($_POST['role'] ?? '');
            if (empty($name)) { $errors[] = 'User name cannot be empty.'; } elseif (strlen($name) < 2 || strlen($name) > 100) { $errors[] = 'Name must be between 2 and 100 characters.'; }
            if (!in_array($role, $validRoles, true)) { $errors[] = 'Invalid user role selected.'; }
            if (empty($errors)) { try { $userModel->update($userId, $name, $role); if ((int)$userId === (int)$_SESSION['user_id']) { $_SESSION['user_name'] = $name; $_SESSION['user_role'] = $role; } set_flash('success', 'User account "' . $name . '" was updated successfully.'); $this->redirect(Auth::baseUrl() . 'admin/users.php'); } catch (\PDOException $e) { $errors[] = 'Database error: Could not update user account.'; } }
        }
        $data = ['pageTitle' => 'Edit User #' . $targetUser['id'], 'adminUser' => $adminUser, 'targetUser' => $targetUser, 'errors' => $errors, 'validRoles' => $validRoles];
        include APP_ROOT . '/app/Views/admin/user_edit.php';
        exit;
    }

    public function userDelete(): void
    {
        Auth::require_role('admin');
        $userId = filter_var($_POST['id'] ?? $_GET['id'] ?? null, FILTER_VALIDATE_INT);
        $currentAdmin = Auth::currentUser();
        if (!$userId || $userId <= 0) { set_flash('error', 'Invalid user specified for deletion.'); $this->redirect(Auth::baseUrl() . 'admin/users.php'); }
        if ((int)$userId === (int)$currentAdmin['id']) { set_flash('error', 'You cannot delete your own active administrator account.'); $this->redirect(Auth::baseUrl() . 'admin/users.php'); }
        try {
            $userModel = new User();
            $user = $userModel->findById($userId);
            if (!$user) { set_flash('error', 'User does not exist.'); } else { $userModel->delete($userId); set_flash('success', 'User account "' . $user['name'] . '" was deleted successfully.'); }
        } catch (\PDOException $e) { set_flash('error', 'Cannot delete user: This account is linked to active transactions.'); }
        $this->redirect(Auth::baseUrl() . 'admin/users.php');
    }
}
