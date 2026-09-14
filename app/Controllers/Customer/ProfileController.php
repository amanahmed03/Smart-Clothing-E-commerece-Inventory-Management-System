<?php
namespace App\Controllers\Customer;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    public function profile(): void
    {
        Auth::require_role('customer');
        $currentUser = Auth::currentUser();
        $userModel = new User();
        $user = $userModel->findById($currentUser['id']);
        if (!$user) {
            set_flash('error', 'Unable to retrieve profile data.');
            $this->redirect(Auth::baseUrl() . 'customer/dashboard.php');
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            if (empty($name)) { $errors[] = 'Full Name cannot be empty.'; }
            elseif (strlen($name) < 2 || strlen($name) > 100) { $errors[] = 'Name must be between 2 and 100 characters.'; }
            if (empty($errors)) {
                try {
                    $userModel->update($currentUser['id'], $name, $currentUser['role']);
                    $_SESSION['user_name'] = $name;
                    set_flash('success', 'Your profile details have been updated successfully.');
                    $this->redirect(Auth::baseUrl() . 'customer/profile.php');
                } catch (\PDOException $e) {
                    $errors[] = 'Database error: Unable to update profile.';
                }
            }
        }
        $data = [
            'pageTitle' => 'My Profile',
            'currentUser' => $currentUser,
            'user' => $user,
            'errors' => $errors
        ];
        include APP_ROOT . '/app/Views/customer/profile.php';
        exit;
    }
}
