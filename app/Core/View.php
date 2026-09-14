<?php
namespace App\Core;

class View
{
    public static function render(string $view, string $layout = 'main', array $data = []): void
    {
        extract($data);
        $viewPath = APP_ROOT . "/app/Views/{$view}.php";

        ob_start();
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "<!-- View not found: {$viewPath} -->";
        }
        $content = ob_get_clean();

        $layoutPath = APP_ROOT . "/app/Views/layouts/{$layout}.php";
        if (file_exists($layoutPath)) {
            include $layoutPath;
        } else {
            echo $content;
        }
    }
}
