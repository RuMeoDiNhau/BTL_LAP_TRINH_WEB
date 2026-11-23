<?php

/** Router tối giản: nhận closure hoặc handler 'Namespace\Class@method'. Hỗ trợ {param}. */
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
        [$handler, $params] = self::match($method, $path);

        if ($handler === null) {
            http_response_code(404);
            echo '404 - Không tìm thấy trang';
            return;
        }

        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
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
        call_user_func_array([$controller, $parts[1]], $params);
    }

    /** @return array{0: callable|string|null, 1: array<int, string>} */
    private static function match(string $method, string $path): array
    {
        $routes = self::$routes[$method] ?? [];
        if (isset($routes[$path])) {
            return [$routes[$path], []];
        }

        $pathParts = explode('/', trim($path, '/'));
        foreach ($routes as $pattern => $handler) {
            if (strpos($pattern, '{') === false) {
                continue;
            }
            $patternParts = explode('/', trim($pattern, '/'));
            if (count($patternParts) !== count($pathParts)) {
                continue;
            }
            $params = [];
            $ok = true;
            foreach ($patternParts as $i => $segment) {
                if (preg_match('/^\{([a-zA-Z_][a-zA-Z0-9_]*)\}$/', $segment, $m)) {
                    $params[] = $pathParts[$i];
                } elseif ($segment !== $pathParts[$i]) {
                    $ok = false;
                    break;
                }
            }
            if ($ok) {
                return [$handler, $params];
            }
        }

        return [null, []];
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
