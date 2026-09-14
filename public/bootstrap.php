<?php

define('APP_ROOT', dirname(__DIR__));

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    $relativeClass = substr($class, strlen($prefix));
    $file = APP_ROOT . '/app/' . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

require_once APP_ROOT . '/app/Core/Database.php';
require_once APP_ROOT . '/app/Core/Session.php';
require_once APP_ROOT . '/app/Core/Auth.php';
require_once APP_ROOT . '/app/Core/helpers.php';

\App\Core\Database::getInstance();
