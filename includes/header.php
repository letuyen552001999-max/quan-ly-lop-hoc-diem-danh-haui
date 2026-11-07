<?php
if (!headers_sent()) {
    header('Cache-Control: no-store, private');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
}
$page_title = $page_title ?? 'Quản lý lớp học và điểm danh';
$user = current_user();
$current = basename($_SERVER['SCRIPT_NAME'] ?? '');
$flash_message = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($page_title) ?> | Nhóm 15 – HaUI</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="topbar">
  <div class="brand"><span class="brand-icon">H</span><div><strong>HaUI – Nhóm 15</strong><small>Quản lý lớp học và điểm danh</small></div></div>
  <?php if ($user): ?>
    <div class="top-account"><span><?= h($user['TenDangNhap']) ?> · <?= h(role_name($user['VaiTro'])) ?></span>
      <form method="post" action="logout.php"><?= csrf_field() ?><button type="submit" class="btn btn-light btn-sm">Đăng xuất</button></form>
    </div>
  <?php endif; ?>
</header>
<?php if ($user): ?>
<div class="layout">
<aside class="sidebar">
  <div class="sidebar-label">CHỨC NĂNG</div>
  <a class="nav-item <?= $current === 'index.php' ? 'active' : '' ?>" href="index.php">⌂ &nbsp; Tổng quan</a>
  <a class="nav-item <?= in_array($current,['classes.php','class_form.php'],true)?'active':'' ?>" href="classes.php">▦ &nbsp; Danh sách lớp</a>
  <?php if ($user['VaiTro'] !== 'SINH_VIEN'): ?>
  <a class="nav-item <?= in_array($current,['students.php','student_form.php'],true) ? 'active' : '' ?>" href="students.php">♧ &nbsp; Quản lý sinh viên</a>
  <?php endif; ?>
  <?php if ($user['VaiTro'] === 'QUAN_TRI_VIEN'): ?><a class="nav-item <?= in_array($current,['teachers.php','teacher_form.php'],true)?'active':'' ?>" href="teachers.php">♙ &nbsp; Giảng viên</a><?php endif; ?>
  <a class="nav-item <?= in_array($current,['schedule.php','schedule_form.php'],true)?'active':'' ?>" href="schedule.php">▤ &nbsp; Lịch học</a>
  <?php if ($user['VaiTro'] !== 'SINH_VIEN'): ?><a class="nav-item <?= in_array($current,['lessons.php','lesson_form.php'],true)?'active':'' ?>" href="lessons.php">▣ &nbsp; Buổi học</a><?php endif; ?>
  <?php if ($user['VaiTro'] !== 'SINH_VIEN'): ?>
  <a class="nav-item <?= $current === 'attendance.php' ? 'active' : '' ?>" href="attendance.php">✓ &nbsp; Điểm danh</a>
  <?php endif; ?>
  <a class="nav-item <?= $current === 'history.php' ? 'active' : '' ?>" href="history.php">◷ &nbsp; Lịch sử điểm danh</a>
  <a class="nav-item <?= $current === 'account.php' ? 'active' : '' ?>" href="account.php">⚙ &nbsp; Đổi mật khẩu</a>
  <?php if ($user['VaiTro'] === 'SINH_VIEN'): ?>
  <a class="nav-item <?= $current === 'notifications.php' ? 'active' : '' ?>" href="notifications.php">♢ &nbsp; Thông báo</a>
  <?php endif; ?>
</aside>
<main class="main">
<?php else: ?>
<main class="main auth-main">
<?php endif; ?>
<?php if ($flash_message): ?>
<div class="alert <?= ($flash_message['type'] ?? '') === 'success' ? 'success' : 'error' ?>"><?= h($flash_message['text'] ?? '') ?></div>
<?php endif; ?>
