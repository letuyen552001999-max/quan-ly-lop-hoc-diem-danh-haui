<?php
require_once __DIR__ . '/includes/bootstrap.php';
require_role('QUAN_TRI_VIEN');
require_post();
verify_csrf();
$id = trim((string) ($_POST['id'] ?? ''));
if ($id === '') { flash('error', 'Thiếu mã sinh viên.'); redirect('students.php'); }
try {
    $stmt = db()->prepare('DELETE FROM SINHVIEN WHERE MaSV = ?');
    $stmt->execute([$id]);
    flash($stmt->rowCount() ? 'success' : 'error', $stmt->rowCount() ? "Đã xóa sinh viên {$id}." : 'Không tìm thấy sinh viên cần xóa.');
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        flash('error', 'Không thể xóa sinh viên đã có điểm danh, tài khoản hoặc thông báo. Cần giữ lịch sử dữ liệu.');
    } else {
        error_log($e->getMessage());
        flash('error', 'Xóa sinh viên thất bại.');
    }
}
redirect('students.php');
