<?php
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_login();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post();
    verify_csrf();
    $old = (string)($_POST['old_password'] ?? '');
    $new = (string)($_POST['new_password'] ?? '');
    $confirm = (string)($_POST['confirm_password'] ?? '');
    $errors = password_errors($new);
    if ($new !== $confirm) $errors[] = 'Mật khẩu nhập lại chưa trùng khớp.';
    if ($new === $old) $errors[] = 'Mật khẩu mới phải khác mật khẩu cũ.';
    if (!$errors) {
        $stmt = db()->prepare('SELECT MatKhauHash FROM TAIKHOAN WHERE MaTK=? AND TrangThai=1');
        $stmt->execute([(int)$user['MaTK']]);
        $hash = $stmt->fetchColumn();
        if (!is_string($hash) || !password_verify($old, $hash)) {
            $errors[] = 'Mật khẩu hiện tại không chính xác.';
        } else {
            try {
                $stmt = db()->prepare('UPDATE TAIKHOAN SET MatKhauHash=? WHERE MaTK=? AND MatKhauHash=? AND TrangThai=1');
                $stmt->execute([password_hash($new, PASSWORD_DEFAULT), (int)$user['MaTK'], $hash]);
                if ($stmt->rowCount() !== 1) {
                    $errors[] = 'Tài khoản vừa thay đổi từ phiên khác. Hãy tải lại trang.';
                } else {
                    session_regenerate_id(true);
                    flash('success', 'Đã đổi mật khẩu. Hãy dùng mật khẩu mới khi đăng nhập lần sau.');
                    redirect('index.php');
                }
            } catch (PDOException $e) {
                error_log($e->getMessage());
                $errors[] = 'Chưa cập nhật được mật khẩu. Vui lòng thử lại.';
            }
        }
    }
}
$page_title = 'Đổi mật khẩu';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><div><h1>Đổi mật khẩu</h1><p>Tài khoản: <?= h($user['TenDangNhap']) ?> · <?= h(role_name($user['VaiTro'])) ?></p></div></div>
<div class="panel panel-narrow">
<?php foreach ($errors as $error): ?><div class="alert error"><?= h($error) ?></div><?php endforeach; ?>
<form method="post"><?= csrf_field() ?>
<div class="field"><label for="old">Mật khẩu hiện tại *</label><input type="password" id="old" name="old_password" required autocomplete="current-password"></div>
<div class="field"><label for="new">Mật khẩu mới *</label><input type="password" id="new" name="new_password" required minlength="10" maxlength="72" autocomplete="new-password"></div>
<div class="field"><label for="confirm">Nhập lại mật khẩu mới *</label><input type="password" id="confirm" name="confirm_password" required minlength="10" maxlength="72" autocomplete="new-password"></div>
<div class="actions"><button class="btn" type="submit">Lưu mật khẩu mới</button><a class="btn btn-light" href="index.php">Hủy</a></div>
<p class="muted-small">Mật khẩu mới có ít nhất 10 ký tự và không quá 72 byte. Hệ thống chỉ lưu mật khẩu dạng băm.</p>
</form>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
