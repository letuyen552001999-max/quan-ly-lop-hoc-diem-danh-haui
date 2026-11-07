<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('QUAN_TRI_VIEN');
$pdo = db();
$id = trim((string) ($_GET['id'] ?? $_POST['original_id'] ?? ''));
$is_edit = $id !== '';
$student = ['MaSV' => '', 'HoTen' => '', 'MaLop' => ''];
if ($is_edit) {
    $stmt = $pdo->prepare('SELECT MaSV, HoTen, MaLop FROM SINHVIEN WHERE MaSV=?');
    $stmt->execute([$id]);
    $student = $stmt->fetch();
    if (!$student) { flash('error', 'Không tìm thấy sinh viên.'); redirect('students.php'); }
    $originalClass = $student['MaLop'];
}
$classes = $pdo->query('SELECT MaLop, TenLop FROM LOP ORDER BY MaLop')->fetchAll();
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $student = [
        'MaSV' => strtoupper(trim((string)($_POST['MaSV'] ?? ''))),
        'HoTen' => trim((string)($_POST['HoTen'] ?? '')),
        'MaLop' => trim((string)($_POST['MaLop'] ?? '')),
    ];
    if (!preg_match('/^[A-Z0-9_-]{2,20}$/', $student['MaSV'])) $errors[] = 'Mã sinh viên gồm 2–20 ký tự A–Z, 0–9, _ hoặc -.';
    if ($student['HoTen'] === '' || text_length($student['HoTen']) > 100) $errors[] = 'Họ tên bắt buộc và không quá 100 ký tự.';
    if (!in_array($student['MaLop'], array_column($classes, 'MaLop'), true)) $errors[] = 'Hãy chọn một lớp đang có trong hệ thống.';
    // Không chuyển lớp sau khi đã có điểm danh, vì bản ghi cũ phải khớp lớp của buổi học.
    if (!$errors && $is_edit && $student['MaLop'] !== ($originalClass ?? '')) {
        $check = $pdo->prepare('SELECT COUNT(*) FROM DIEMDANH WHERE MaSV=?');
        $check->execute([$id]);
        if ((int)$check->fetchColumn() > 0) {
            $errors[] = 'Sinh viên đã có lịch sử điểm danh, không thể chuyển lớp trong bản demo này.';
        }
    }
    if (!$errors) {
        try {
            if ($is_edit) {
                // Không đổi mã sinh viên để giữ liên kết điểm danh/tài khoản.
                $stmt = $pdo->prepare('UPDATE SINHVIEN SET HoTen=?, MaLop=? WHERE MaSV=?');
                $stmt->execute([$student['HoTen'], $student['MaLop'], $id]);
                flash('success', 'Đã cập nhật sinh viên ' . $id . '.');
            } else {
                $stmt = $pdo->prepare('INSERT INTO SINHVIEN (MaSV,HoTen,MaLop) VALUES (?,?,?)');
                $stmt->execute([$student['MaSV'], $student['HoTen'], $student['MaLop']]);
                flash('success', 'Đã thêm sinh viên ' . $student['MaSV'] . '.');
            }
            redirect('students.php');
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $errors[] = 'Mã sinh viên bị trùng hoặc thông tin đang liên kết dữ liệu khác.';
            } else {
                error_log($e->getMessage());
                $errors[] = 'Không lưu được dữ liệu. Vui lòng kiểm tra CSDL.';
            }
        }
    }
}
$page_title = $is_edit ? 'Sửa sinh viên' : 'Thêm sinh viên';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><div><h1><?= $is_edit ? 'Sửa thông tin sinh viên' : 'Thêm sinh viên' ?></h1><p>Nhập đầy đủ các trường có đánh dấu bắt buộc.</p></div><a href="students.php" class="btn btn-light">Quay lại</a></div>
<div class="panel panel-narrow">
<?php foreach($errors as $error): ?><div class="alert error"><?= h($error) ?></div><?php endforeach; ?>
<form method="post"><?= csrf_field() ?>
<?php if ($is_edit): ?><input type="hidden" name="original_id" value="<?= h($id) ?>"><?php endif; ?>
<div class="field"><label for="MaSV">Mã sinh viên *</label><input name="MaSV" id="MaSV" maxlength="20" required value="<?= h($student['MaSV']) ?>" <?= $is_edit ? 'readonly' : '' ?> placeholder="Ví dụ: SV004"></div>
<div class="field"><label for="HoTen">Họ và tên *</label><input name="HoTen" id="HoTen" maxlength="100" required value="<?= h($student['HoTen']) ?>" placeholder="Ví dụ: Nguyễn Văn Bình"></div>
<div class="field"><label for="MaLop">Lớp học *</label><select id="MaLop" name="MaLop" required><option value="">-- Chọn lớp --</option><?php foreach ($classes as $lop): ?><option value="<?= h($lop['MaLop']) ?>" <?= $student['MaLop'] === $lop['MaLop']?'selected':'' ?>><?= h($lop['MaLop'].' - '.$lop['TenLop']) ?></option><?php endforeach; ?></select></div>
<div class="actions"><button class="btn" type="submit"><?= $is_edit ? 'Lưu thay đổi' : 'Thêm sinh viên' ?></button><a class="btn btn-light" href="students.php">Hủy</a></div>
</form></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
