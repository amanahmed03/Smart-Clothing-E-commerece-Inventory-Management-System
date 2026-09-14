<?php

function base_url(string $path = ''): string
{
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $base = '/';
    if (strpos($scriptName, '/smart_clothing') !== false) {
        $base = '/smart_clothing/';
    }
    return $base . ltrim($path, '/');
}

function e($data): string
{
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void
{
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }
    echo '<script>window.location.href="' . htmlspecialchars($url, ENT_QUOTES) . '";</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url, ENT_QUOTES) . '"></noscript>';
    exit;
}

function set_flash(string $type, string $message): void
{
    \App\Core\Session::flash($type, $message);
}

function has_flash(string $type): bool
{
    return !empty($_SESSION['flash'][$type] ?? null);
}

function get_flash(string $type): ?string
{
    return \App\Core\Session::getFlash($type);
}

function current_user(): ?array
{
    return \App\Core\Auth::currentUser();
}

function is_logged_in(): bool
{
    return \App\Core\Auth::isLoggedIn();
}

function require_login(): void
{
    \App\Core\Auth::requireLogin();
}

function require_role($roles): void
{
    \App\Core\Auth::requireRole($roles);
}
