<?php
require_once __DIR__ . '/includes/bootstrap.php';
// Trang khởi tạo chỉ mở từ máy đang chạy XAMPP, và chỉ khi CSDL chưa có tài khoản.
$client = $_SERVER['REMOTE_ADDR'] ?? '';
if (!in_array($client, ['127.0.0.1', '::1'], true)) {
    http_response_code(403);
    exit('Trang tạo tài khoản ban đầu chỉ dùng trên chính máy cài XAMPP (localhost).');
}
$count = (int) db()->query('SELECT COUNT(*) FROM TAIKHOAN')->fetchColumn();
if ($count > 0) {
    flash('error', 'Hệ thống đã tạo tài khoản. Đăng nhập để sử dụng.');
    redirect('login.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $admin = (string) ($_POST['admin_password'] ?? '');
    $teacher = (string) ($_POST['teacher_password'] ?? '');
    $student = (string) ($_POST['student_password'] ?? '');
    if (password_errors($admin) || password_errors($teacher) || password_errors($student)) {
        $error = 'Mỗi mật khẩu phải dài tối thiểu 10 ký tự, không quá 72 byte.';
    } elseif ($admin === $teacher || $admin === $student || $teacher === $student) {
        $error = 'Ba tài khoản mẫu phải có mật khẩu khác nhau.';
    } else {
        try {
            $pdo = db();
            $pdo->beginTransaction();
            $insert = $pdo->prepare('INSERT INTO TAIKHOAN (TenDangNhap, MatKhauHash, VaiTro, MaSV, MaGV) VALUES (?, ?, ?, ?, ?)');
            $insert->execute(['admin', password_hash($admin, PASSWORD_DEFAULT), 'QUAN_TRI_VIEN', null, null]);
            $insert->execute(['gv01', password_hash($teacher, PASSWORD_DEFAULT), 'GIANG_VIEN', null, 'GV001']);
            $insert->execute(['sv001', password_hash($student, PASSWORD_DEFAULT), 'SINH_VIEN', 'SV001', null]);
            $pdo->commit();
            flash('success', 'Đã tạo 3 tài khoản mẫu: admin, gv01, sv001. Đăng nhập bằng mật khẩu bạn vừa đặt.');
            redirect('login.php');
        } catch (Throwable $e) {
            if (db()->inTransaction()) db()->rollBack();
            error_log($e->getMessage());
            $error = 'Không tạo được tài khoản. Kiểm tra đã nhập schema.sql và có GV001, SV001.';
        }
    }
}
$page_title = 'Khởi tạo tài khoản';
require __DIR__ . '/includes/header.php';
?>
<div class="panel"><div class="auth-title"><h1>Tạo tài khoản dùng thử</h1><p>Chỉ thực hiện một lần trên localhost sau khi nhập CSDL.</p></div>
<?php if ($error): ?><div class="alert error"><?= h($error) ?></div><?php endif; ?>
<div class="role-note">Sẽ tạo ba tài khoản: <b>admin</b> (Quản trị viên), <b>gv01</b> (Giảng viên), <b>sv001</b> (Sinh viên). Mật khẩu do bạn tự đặt, không lưu dạng văn bản thuần.</div>
<form method="post" style="margin-top:20px"><?= csrf_field() ?>
<div class="field"><label for="admin_password">Mật khẩu admin</label><input type="password" id="admin_password" name="admin_password" required minlength="10" maxlength="72" autocomplete="new-password"></div>
<div class="field"><label for="teacher_password">Mật khẩu gv01</label><input type="password" id="teacher_password" name="teacher_password" required minlength="10" maxlength="72" autocomplete="new-password"></div>
<div class="field"><label for="student_password">Mật khẩu sv001</label><input type="password" id="student_password" name="student_password" required minlength="10" maxlength="72" autocomplete="new-password"></div>
<button class="btn login-button" type="submit">Tạo tài khoản</button>
</form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
