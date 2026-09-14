<?php
namespace App\Core;

class Router
{
    private array $routes = [];

    public function add(string $path, string $controller, string $method, string $type = 'GET'): void
    {
        $this->routes[] = [
            'path' => $path,
            'controller' => $controller,
            'method' => $method,
            'type' => $type
        ];
    }

    public function dispatch(): void
    {
        $uri = $this->getUri();
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes as $route) {
            if ($route['type'] !== $method && $route['type'] !== '*') {
                continue;
            }
            if ($this->matchRoute($route['path'], $uri, $params)) {
                $controllerClass = $route['controller'];
                $controllerMethod = $route['method'];
                if (class_exists($controllerClass)) {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $controllerMethod)) {
                        call_user_func_array([$controller, $controllerMethod], $params);
                        return;
                    }
                }
            }
        }

        http_response_code(404);
        echo "<h1>404 - Page Not Found</h1>";
    }

    private function getUri(): string
    {
        $base = Auth::baseUrl();
        $uri = $_SERVER['REQUEST_URI'];
        if (strpos($uri, '?') !== false) {
            $uri = substr($uri, 0, strpos($uri, '?'));
        }
        if (strpos($uri, $base) === 0) {
            $uri = substr($uri, strlen($base));
        }
        return trim($uri, '/');
    }

    private function matchRoute(string $routePath, string $uri, array &$params = []): bool
    {
        $routeParts = explode('/', trim($routePath, '/'));
        $uriParts = explode('/', trim($uri, '/'));

        if (count($routeParts) !== count($uriParts)) {
            return false;
        }

        $params = [];
        foreach ($routeParts as $i => $part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $paramName = substr($part, 1, -1);
                $params[$paramName] = $uriParts[$i];
            } elseif ($part !== $uriParts[$i]) {
                return false;
            }
        }
        return true;
    }
}
