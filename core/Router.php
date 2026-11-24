<?php

declare(strict_types=1);

/**
 * Router tối giản: get()/post(), tham số {id}/{slug}, tự tính base path.
 * Handler là [Class::class, 'method'] (class do autoload nạp) hoặc callable thường.
 */
class Router
{
    /** @var array<string,array<int,array{regex:string,params:array<int,string>,handler:mixed}>> */
    private $routes = [];

    /** @var string|null */
    private static $basePath = null;

    /**
     * Base path không có dấu / cuối: '' (docroot) hoặc '/btl' (thư mục con XAMPP).
     * Apache root .htaccess rewrite vào public/: SCRIPT_NAME có /public nhưng URI thì không.
     */
    public static function basePath(): string
    {
        if (self::$basePath !== null) {
            return self::$basePath;
        }

        if (defined('BASE_URL') && is_string(BASE_URL) && BASE_URL !== '') {
            $parts = parse_url(BASE_URL);
            self::$basePath = rtrim((string) ($parts['path'] ?? ''), '/');
            return self::$basePath;
        }

        $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $base = str_replace('\\', '/', dirname($script));
        if ($base === '.' || $base === '/' || $base === '') {
            $base = '';
        } else {
            $base = rtrim($base, '/');
            $uri = str_replace('\\', '/', (string) ($_SERVER['REQUEST_URI'] ?? ''));
            if (substr($base, -7) === '/public' && strpos($uri, $base . '/') !== 0) {
                $base = substr($base, 0, -7);
            }
        }

        self::$basePath = $base;
        return $base;
    }

    /** @param mixed $handler */
    public function get(string $pattern, $handler): void
    {
        $this->add('GET', $pattern, $handler);
    }

    /** @param mixed $handler */
    public function post(string $pattern, $handler): void
    {
        $this->add('POST', $pattern, $handler);
    }

    /** @param mixed $handler */
    private function add(string $method, string $pattern, $handler): void
    {
        $pattern = '/' . trim($pattern, '/');
        $params = [];
        $regex = '';
        $offset = 0;

        if (preg_match_all('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', $pattern, $matches, PREG_OFFSET_CAPTURE) > 0) {
            foreach ($matches[0] as $i => $token) {
                $regex .= preg_quote(substr($pattern, $offset, (int) $token[1] - $offset), '#');
                $name = (string) $matches[1][$i][0];
                $regex .= '(?P<' . $name . '>[^/]+)';
                $params[] = $name;
                $offset = (int) $token[1] + strlen((string) $token[0]);
            }
        }
        $regex .= preg_quote(substr($pattern, $offset), '#');
        $regex = $pattern === '/' ? '#^/$#' : '#^' . $regex . '/?$#';

        $this->routes[$method][] = [
            'regex'   => $regex,
            'params'  => $params,
            'handler' => $handler,
        ];
    }

    public function dispatch(): void
    {
        $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $path = $this->currentPath();

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $path, $m) !== 1) {
                continue;
            }

            $args = [];
            foreach ($route['params'] as $name) {
                $value = (string) ($m[$name] ?? '');
                // Controller có strict_types: truyền int cho {id} để tránh TypeError.
                $args[] = ctype_digit($value) ? (int) $value : $value;
            }

            $handler = $route['handler'];
            if (is_array($handler) && count($handler) === 2 && is_string($handler[0]) && !is_callable($handler)) {
                $class = $handler[0];
                if (!class_exists($class)) {
                    $this->serverError('Không tìm thấy controller: ' . $class);
                    return;
                }
                $handler = [new $class(), $handler[1]];
            }
            if (!is_callable($handler)) {
                $this->serverError('Handler route không hợp lệ: ' . $path);
                return;
            }

            call_user_func_array($handler, $args);
            return;
        }

        $this->notFound();
    }

    public function currentPath(): string
    {
        $raw = parse_url((string) ($_SERVER['REQUEST_URI'] ?? '/'), PHP_URL_PATH);
        $path = rawurldecode(is_string($raw) ? $raw : '/');
        $base = self::basePath();

        if ($base !== '') {
            if ($path === $base) {
                $path = '/';
            } elseif (strpos($path, $base . '/') === 0) {
                $path = substr($path, strlen($base));
            }
        }

        $path = '/' . trim($path, '/');
        return $path === '' ? '/' : $path;
    }

    private function notFound(): void
    {
        http_response_code(404);
        $file = dirname(__DIR__) . '/app/Views/errors/404.php';
        if (is_file($file)) {
            require $file;
            return;
        }
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>404 Not Found</title></head>'
            . '<body><h1>404 – Không tìm thấy trang</h1></body></html>';
    }

    private function serverError(string $message): void
    {
        http_response_code(500);
        header('Content-Type: text/html; charset=utf-8');
        echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>500 Error</title></head>'
            . '<body><h1>500 – Lỗi máy chủ</h1><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p></body></html>';
    }
}
