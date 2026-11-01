<?php

/**
 * File mẫu cấu hình kết nối CSDL (PDO).
 *
 * Cách dùng:
 *   1. Sao chép file này thành config/database.php
 *   2. Điền thông tin MariaDB/MySQL thật của máy bạn
 *
 * Không commit config/database.php lên Git vì file đó chứa mật khẩu.
 * File này dùng chung driver/DSN "mysql:" cho cả MySQL và MariaDB.
 */
return [
    'host'     => '127.0.0.1',
    'port'     => '3306',
    'dbname'   => 'decor_db',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
];
