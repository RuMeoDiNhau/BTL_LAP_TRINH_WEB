<?php

/** Router tối giản: nhận closure hoặc handler 'Namespace\Class@method'. */
final class Router
{
    /** @var array<string, array<string, callable|string>> */
    private static array $routes = ['GET' => [], 'POST' => []];

    public static function add(string $method, string $path, callable|string $handler): void
    {
        $method = strtoupper($method);
        if (!isset(self::$routes[$method])) {
            self::$routes[$method] = [];
        }

        $path = '/' . trim($path, '/');
        self::$routes[$method][$path] = $handler;
    }

    public static function dispatch(): void
    {
        $method = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        $path = self::currentPath();
        $handler = self::$routes[$method][$path] ?? null;

        if ($handler === null) {
            http_response_code(404);
            echo '404 - Không tìm thấy trang';
            return;
        }

        if (is_callable($handler)) {
            $handler();
            return;
        }

        $parts = explode('@', $handler, 2);
        if (count($parts) !== 2 || !class_exists($parts[0]) || !method_exists($parts[0], $parts[1])) {
            http_response_code(500);
            echo '500 - Không tìm thấy controller';
            return;
        }

        /** @var object $controller */
        $controller = new $parts[0]();
        $controller->{$parts[1]}();
    }

    /** Cắt BASE_URL (vd /btl) khỏi REQUEST_URI trước khi khớp route. */
    private static function currentPath(): string
    {
        $uri = (string)(parse_url((string)($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH) ?: '/');
        $uri = rawurldecode($uri);
        $base = base_url();

        if ($base !== '') {
            if ($uri === $base) {
                $uri = '/';
            } elseif (str_starts_with($uri, $base . '/')) {
                $uri = substr($uri, strlen($base));
            }
        }

        $uri = '/' . trim($uri, '/');
        return $uri === '' ? '/' : $uri;
    }
}
