<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/validation.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    if (!headers_sent()) {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.cookie_httponly', '1');
        ini_set('session.cookie_samesite', 'Lax');
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            ini_set('session.cookie_secure', '1');
        }
    }
    session_start();
}

function h(?string $value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function redirect(string $destination): void
{
    header('Location: ' . $destination);
    exit;
}
function flash(string $type, string $text): void
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}
function csrf_token(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}
function verify_csrf(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    if (!is_string($submitted) || !hash_equals(csrf_token(), $submitted)) {
        http_response_code(400);
        exit('Yêu cầu không hợp lệ. Hãy tải lại trang rồi thử lại.');
    }
}
function require_post(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        exit('Phương thức không được hỗ trợ.');
    }
}
function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}
function require_login(): array
{
    $user = current_user();
    if (!$user) {
        redirect('login.php');
    }
    return $user;
}
function has_role(string ...$roles): bool
{
    return current_user() && in_array(current_user()['VaiTro'], $roles, true);
}
function require_role(string ...$roles): array
{
    $user = require_login();
    if (!in_array($user['VaiTro'], $roles, true)) {
        http_response_code(403);
        exit('Bạn không có quyền thực hiện chức năng này. <a href="index.php">Về trang chủ</a>');
    }
    return $user;
}
function role_name(string $role): string
{
    return match ($role) {
        'QUAN_TRI_VIEN' => 'Quản trị viên',
        'GIANG_VIEN' => 'Giảng viên',
        'SINH_VIEN' => 'Sinh viên',
        default => 'Người dùng',
    };
}
function value_or_null(mixed $v): ?string
{
    $v = trim((string)$v);
    return $v === '' ? null : $v;
}
