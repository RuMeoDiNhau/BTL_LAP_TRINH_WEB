<?php

declare(strict_types=1);

/** Kết nối PDO dùng chung trong một request, tương thích với các model của nhóm. */
final class Database
{
    private static ?self $instance = null;
    private PDO $connection;

    private function __construct()
    {
        $file = dirname(__DIR__) . '/config/database.php';
        if (!is_file($file) && getenv('DECOR_DB_HOST') === false) {
            throw new RuntimeException('Chưa có config/database.php. Hãy sao chép config/database.example.php và điền cấu hình MySQL.');
        }
        $config = require is_file($file) ? $file : dirname(__DIR__) . '/config/database.example.php';
        foreach (['host', 'port', 'dbname', 'username', 'password', 'charset'] as $key) {
            $value = getenv('DECOR_DB_' . strtoupper($key));
            if ($value !== false) {
                $config[$key] = $value;
            }
        }
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'], $config['port'], $config['dbname'], $config['charset']);
        $this->connection = new PDO($dsn, $config['username'], $config['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function getInstance(): self
    {
        return self::$instance ??= new self();
    }

    public function getConnection(): PDO
    {
        return $this->connection;
    }
}
