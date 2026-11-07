<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect('index.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $attempts = (int)($_SESSION['failed_logins'] ?? 0);
    $last = (int)($_SESSION['last_failed_at'] ?? 0);
    if ($attempts >= 7 && time()-$last < 300) {
        $error = 'Đăng nhập sai nhiều lần. Hãy thử lại sau 5 phút.';
    } else {
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $stmt = db()->prepare('SELECT MaTK, TenDangNhap, MatKhauHash, VaiTro, MaSV, MaGV, TrangThai FROM TAIKHOAN WHERE TenDangNhap = ? LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    if ($user && (int)$user['TrangThai'] === 1 && password_verify($password, $user['MatKhauHash'])) {
        session_regenerate_id(true);
        unset($user['MatKhauHash'], $user['TrangThai']);
        $_SESSION['user'] = $user;
        unset($_SESSION['failed_logins'], $_SESSION['last_failed_at']);
        redirect('index.php');
    }
    $_SESSION['failed_logins'] = ($attempts>=7 && time()-$last>=300) ? 1 : $attempts + 1;
    $_SESSION['last_failed_at'] = time();
    $error = 'Tên đăng nhập hoặc mật khẩu chưa đúng.';
    }
}
$page_title = 'Đăng nhập';
require __DIR__ . '/includes/header.php';
?>
<div class="panel">
<div class="auth-title"><div style="font-size:44px;color:#4338ca">▣</div><h1>Đăng nhập hệ thống</h1><p>Cổng quản lý lớp học và điểm danh sinh viên</p></div>
<?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
<form method="post"><?= csrf_field() ?>
  <div class="field"><label for="username">TÀI KHOẢN</label><input id="username" name="username" required maxlength="50" autofocus placeholder="Nhập tên đăng nhập" autocomplete="username"></div>
  <div class="field"><label for="password">MẬT KHẨU</label><input type="password" id="password" name="password" required placeholder="Nhập mật khẩu" autocomplete="current-password"></div>
  <button class="btn login-button" type="submit">ĐĂNG NHẬP</button>
</form>
<p class="muted-small" style="text-align:center">Lần đầu chạy? Nhập CSDL rồi mở <a href="setup.php">Khởi tạo tài khoản</a> trên localhost.</p>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
