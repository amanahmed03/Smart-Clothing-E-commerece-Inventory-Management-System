<?php
namespace App\Core;

class Controller
{
    protected \PDO $pdo;
    protected array $data = [];

    public function __construct()
    {
        $this->pdo = Database::getInstance();
    }

    public function set(string $key, $value): void
    {
        $this->data[$key] = $value;
    }

    public function setData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    public function getData(): array
    {
        return $this->data;
    }

    protected function render(string $view, string $layout = 'main'): void
    {
        View::render($view, $layout, $this->data);
        exit;
    }

    protected function redirect(string $url): void
    {
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }
        echo '<script>window.location.href="' . htmlspecialchars($url, ENT_QUOTES) . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url, ENT_QUOTES) . '"></noscript>';
        exit;
    }
}
