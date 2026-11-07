<?php
/** Kết nối MySQL/MariaDB qua PDO. Cấu hình mặc định chỉ dùng trên XAMPP cục bộ. */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $host = getenv('APP_DB_HOST') ?: '127.0.0.1';
    $name = getenv('APP_DB_NAME') ?: 'ql_lophoc_diemdanh';
    $user = getenv('APP_DB_USER') ?: 'root';
    $pass = getenv('APP_DB_PASS');
    $pass = ($pass === false) ? '' : $pass;
    $port = getenv('APP_DB_PORT') ?: '3306';
    try {
        $pdo = new PDO(
            "mysql:host={$host};port={$port};dbname={$name};charset=utf8mb4",
            $user,
            $pass,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]
        );
        return $pdo;
    } catch (PDOException $e) {
        error_log('Database connection failed: ' . $e->getMessage());
        http_response_code(503);
        exit('Không kết nối được cơ sở dữ liệu. Kiểm tra MySQL/XAMPP và cấu hình tại config/database.php.');
    }
}
