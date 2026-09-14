<?php
namespace App\Core;

use App\Models\User;

class Auth
{
    public static function isLoggedIn(): bool
    {
        return !empty(Session::get('user_id')) && !empty(Session::get('user_role'));
    }

    public static function currentUser(): ?array
    {
        if (!self::isLoggedIn()) {
            return null;
        }
        return [
            'id' => Session::get('user_id'),
            'name' => Session::get('user_name') ?? 'User',
            'email' => Session::get('user_email') ?? '',
            'role' => Session::get('user_role') ?? 'customer'
        ];
    }

    public static function requireLogin(): void
    {
        if (!self::isLoggedIn()) {
            Session::flash('error', 'Please log in to access this page.');
            self::redirectToLogin();
        }
    }

    public static function require_role($allowedRoles): void
    {
        self::requireRole($allowedRoles);
    }

    public static function requireRole($allowedRoles): void
    {
        self::requireLogin();
        $user = self::currentUser();
        $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
        if (!in_array($user['role'], $roles, true)) {
            Session::flash('error', 'Access denied: You do not have permission to access that section.');
            redirect(self::getDashboardUrl($user['role']));
        }
    }

    public static function login(int $id, string $name, string $email, string $role): void
    {
        Session::regenerateId();
        Session::set('user_id', $id);
        Session::set('user_name', $name);
        Session::set('user_email', $email);
        Session::set('user_role', $role);
    }

    public static function logout(): void
    {
        Session::destroy();
        Session::start();
        Session::flash('success', 'You have been successfully logged out.');
        redirect(self::baseUrl() . 'login.php');
    }

    private static function getDashboardUrl(string $role): string
    {
        $base = self::baseUrl();
        switch ($role) {
            case 'admin': return $base . 'admin/dashboard.php';
            case 'inventory_manager': return $base . 'inventory/dashboard.php';
            case 'delivery_manager': return $base . 'delivery/dashboard.php';
            case 'customer': return $base . 'customer/dashboard.php';
            default: return $base . 'login.php';
        }
    }

    public static function baseUrl(): string
    {
        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        $base = '/';
        if (strpos($scriptName, '/smart_clothing') !== false) {
            $base = '/smart_clothing/';
        }
        return $base;
    }
}
