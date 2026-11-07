<?php
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_role('QUAN_TRI_VIEN', 'GIANG_VIEN');
$q = trim((string)($_GET['q'] ?? ''));
$maLop = trim((string)($_GET['lop'] ?? ''));
$where = [];
$params = [];
if ($q !== '') {
    $where[] = '(s.MaSV LIKE ? OR s.HoTen LIKE ?)';
    $params[] = "%{$q}%"; $params[] = "%{$q}%";
}
if ($maLop !== '') { $where[] = 's.MaLop = ?'; $params[] = $maLop; }
if ($user['VaiTro'] === 'GIANG_VIEN') {
    $where[] = 'EXISTS (SELECT 1 FROM LICHHOC lh WHERE lh.MaLop=s.MaLop AND lh.MaGV=?)';
    $params[] = $user['MaGV'];
}
$sql = 'SELECT s.MaSV, s.HoTen, s.MaLop, l.TenLop FROM SINHVIEN s JOIN LOP l ON l.MaLop=s.MaLop';
if ($where) $sql .= ' WHERE ' . implode(' AND ', $where);
$sql .= ' ORDER BY s.MaLop, s.MaSV LIMIT 300';
$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$classesSql = 'SELECT MaLop, TenLop FROM LOP';
if ($user['VaiTro'] === 'GIANG_VIEN') {
    $classesSql .= ' WHERE MaLop IN (SELECT MaLop FROM LICHHOC WHERE MaGV=?)';
}
$classesSql .= ' ORDER BY MaLop';
$cstmt = db()->prepare($classesSql);
$cstmt->execute($user['VaiTro'] === 'GIANG_VIEN' ? [$user['MaGV']] : []);
$classes = $cstmt->fetchAll();
$page_title = 'Quản lý sinh viên';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><div><h1>Quản lý sinh viên</h1><p>Tra cứu theo mã, họ tên và lớp. Chỉ quản trị viên được thay đổi dữ liệu.</p></div>
<?php if (has_role('QUAN_TRI_VIEN')): ?><a class="btn" href="student_form.php">+ Thêm sinh viên</a><?php endif; ?></div>
<div class="panel"><form method="get" class="filters">
<div class="field"><label for="q">Tìm kiếm</label><input id="q" type="search" name="q" value="<?= h($q) ?>" placeholder="Mã sinh viên hoặc họ tên"></div>
<div class="field"><label for="lop">Lớp học</label><select id="lop" name="lop"><option value="">Tất cả lớp</option><?php foreach ($classes as $lop): ?><option value="<?= h($lop['MaLop']) ?>" <?= $maLop === $lop['MaLop']?'selected':'' ?>><?= h($lop['TenLop']) ?></option><?php endforeach; ?></select></div>
<button class="btn" type="submit">Tìm kiếm</button><a class="btn btn-light" href="students.php">Xóa lọc</a>
</form></div>
<div class="panel scroll"><table class="data-table"><thead><tr><th>Mã sinh viên</th><th>Họ và tên</th><th>Mã lớp</th><th>Tên lớp</th><?php if (has_role('QUAN_TRI_VIEN')): ?><th>Thao tác</th><?php endif; ?></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><td><?= h($row['MaSV']) ?></td><td><?= h($row['HoTen']) ?></td><td><?= h($row['MaLop']) ?></td><td><?= h($row['TenLop']) ?></td>
<?php if (has_role('QUAN_TRI_VIEN')): ?><td><div class="actions"><a class="btn btn-sm btn-light" href="student_form.php?id=<?= rawurlencode($row['MaSV']) ?>">Sửa</a>
<form method="post" action="student_delete.php" class="inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa sinh viên này?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= h($row['MaSV']) ?>"><button class="btn btn-sm btn-danger" type="submit">Xóa</button></form></div></td><?php endif; ?></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="5" class="empty">Không có sinh viên phù hợp.</td></tr><?php endif; ?>
</tbody></table><p class="muted-small">Tối đa 300 kết quả đầu tiên. Dữ liệu mẫu chỉ dùng để kiểm tra chức năng.</p></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
