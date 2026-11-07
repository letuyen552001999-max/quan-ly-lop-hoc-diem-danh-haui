<?php
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_login();
$pdo = db();
$role = $user['VaiTro'];
$stats = [];
if ($role === 'QUAN_TRI_VIEN') {
    $stats['Số lớp học'] = (int) $pdo->query('SELECT COUNT(*) FROM LOP')->fetchColumn();
    $stats['Số sinh viên'] = (int) $pdo->query('SELECT COUNT(*) FROM SINHVIEN')->fetchColumn();
    $stats['Số giảng viên'] = (int) $pdo->query('SELECT COUNT(*) FROM GIANGVIEN')->fetchColumn();
    $stats['Số buổi học'] = (int) $pdo->query('SELECT COUNT(*) FROM BUOIHOC')->fetchColumn();
} elseif ($role === 'GIANG_VIEN') {
    $query = $pdo->prepare('SELECT COUNT(DISTINCT MaLop) FROM LICHHOC WHERE MaGV=?');
    $query->execute([$user['MaGV']]);
    $stats['Lớp phụ trách'] = (int) $query->fetchColumn();
    $query = $pdo->prepare('SELECT COUNT(*) FROM BUOIHOC b JOIN LICHHOC l ON l.MaLich=b.MaLich WHERE l.MaGV=?');
    $query->execute([$user['MaGV']]);
    $stats['Buổi học'] = (int) $query->fetchColumn();
} else {
    $query = $pdo->prepare('SELECT COUNT(*) FROM DIEMDANH WHERE MaSV=?');
    $query->execute([$user['MaSV']]);
    $stats['Buổi đã có điểm danh'] = (int) $query->fetchColumn();
    $query = $pdo->prepare('SELECT COUNT(*) FROM THONGBAO WHERE MaSV=? AND DaXem=0');
    $query->execute([$user['MaSV']]);
    $stats['Thông báo chưa đọc'] = (int) $query->fetchColumn();
}
$page_title = 'Tổng quan';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><div><h1>Trang chủ</h1><p>Xin chào, <?= h($user['TenDangNhap']) ?> – <?= h(role_name($role)) ?>.</p></div><span class="badge good">Dự án minh họa · Nhóm 15</span></div>
<div class="kpi-grid">
<?php foreach ($stats as $label=>$amount): ?>
<div class="kpi"><span><?= h($label) ?></span><strong><?= h((string)$amount) ?></strong></div>
<?php endforeach; ?>
</div>
<div class="panel"><h2>Chức năng chính</h2>
<div class="stack">
<a class="btn btn-light" href="classes.php">Xem danh sách lớp</a>
<a class="btn btn-light" href="schedule.php">Xem lịch học</a>
<?php if ($role === 'QUAN_TRI_VIEN'): ?><a class="btn btn-light" href="teachers.php">Quản lý giảng viên</a><?php endif; ?>
<?php if ($role !== 'SINH_VIEN'): ?><a class="btn btn-light" href="lessons.php">Quản lý buổi học</a><?php endif; ?>
<?php if ($role === 'QUAN_TRI_VIEN'): ?><a class="btn" href="students.php">Quản lý sinh viên</a><?php endif; ?>
<?php if ($role === 'GIANG_VIEN'): ?><a class="btn" href="attendance.php">Thực hiện điểm danh</a><?php endif; ?>
<a class="btn btn-light" href="history.php">Lịch sử điểm danh</a>
<a class="btn btn-light" href="account.php">Đổi mật khẩu</a>
<?php if ($role === 'SINH_VIEN'): ?><a class="btn btn-light" href="notifications.php">Thông báo</a><?php endif; ?>
</div></div>
<div class="panel"><h2>Giới thiệu</h2><p>Hệ thống được xây dựng từ bản phân tích và thiết kế của nhóm 15 trong học phần Thực tập cơ sở ngành. Dữ liệu đang sử dụng là dữ liệu mẫu phục vụ học tập; không kết nối với hệ thống thực tế của nhà trường.</p></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
