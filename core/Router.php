<?php

declare(strict_types=1);

final class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][$path] = $handler;
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][$path] = $handler;
    }

    public function dispatch(string $method, string $path): void
    {
        $path = '/' . trim($path, '/');
        $handler = $this->routes[$method][$path] ?? null;
        if ($handler) {
            $handler();
            return;
        }
        $exists = isset($this->routes['GET'][$path]) || isset($this->routes['POST'][$path]);
        http_response_code($exists ? 405 : 404);
        if ($exists) {
            header('Allow: ' . implode(', ', array_keys(array_filter($this->routes, fn ($routes) => isset($routes[$path])))));
        }
        (new ErrorController())->show($exists ? 'Phương thức không được hỗ trợ.' : 'Không tìm thấy trang bạn yêu cầu.', http_response_code());
    }
}
