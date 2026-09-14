<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::isLoggedIn()) {
            $user = Auth::currentUser();
            $this->redirect(Auth::baseUrl() . $this->getDashboardPath($user['role']));
        }

        $error = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            if (empty($email) || empty($password)) {
                $error = 'Please enter both email and password.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Please enter a valid email address.';
            } else {
                try {
                    $userModel = new User();
                    $user = $userModel->findByEmail($email);

                    if ($user && password_verify($password, $user['password'])) {
                        Auth::login($user['id'], $user['name'], $user['email'], $user['role']);
                        $this->redirect(Auth::baseUrl() . $this->getDashboardPath($user['role']));
                    } else {
                        $error = 'Invalid email or password. Please try again.';
                    }
                } catch (\PDOException $e) {
                    $error = 'A database error occurred. Please try again later.';
                }
            }
        }

        $flashSuccess = get_flash('success');
        $flashError = get_flash('error');

        include APP_ROOT . '/app/Views/auth/login.php';
        exit;
    }

    public function register(): void
    {
        if (Auth::isLoggedIn()) {
            $user = Auth::currentUser();
            $this->redirect(Auth::baseUrl() . $this->getDashboardPath($user['role']));
        }

        $errors = [];
        $name = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($name)) {
                $errors[] = 'Full Name is required.';
            }
            if (empty($email)) {
                $errors[] = 'Email address is required.';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Please provide a valid email address.';
            }
            if (empty($password)) {
                $errors[] = 'Password is required.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Password must be at least 6 characters long.';
            }
            if ($password !== $confirmPassword) {
                $errors[] = 'Passwords do not match.';
            }

            if (empty($errors)) {
                try {
                    $userModel = new User();
                    $existingUser = $userModel->findByEmail($email);

                    if ($existingUser) {
                        $errors[] = 'An account with this email address already exists. Please log in.';
                    } else {
                        $userModel->create($name, $email, $password, 'customer');
                        set_flash('success', 'Registration successful! You can now log in with your credentials.');
                        $this->redirect(Auth::baseUrl() . 'login.php');
                    }
                } catch (\PDOException $e) {
                    $errors[] = 'Database error: Could not complete registration. Please try again.';
                }
            }
        }

        include APP_ROOT . '/app/Views/auth/register.php';
        exit;
    }

    public function logout(): void
    {
        Auth::logout();
    }

    private function getDashboardPath(string $role): string
    {
        switch ($role) {
            case 'admin': return 'admin/dashboard.php';
            case 'inventory_manager': return 'inventory/dashboard.php';
            case 'delivery_manager': return 'delivery/dashboard.php';
            case 'customer': return 'customer/dashboard.php';
            default: return 'login.php';
        }
    }
}
