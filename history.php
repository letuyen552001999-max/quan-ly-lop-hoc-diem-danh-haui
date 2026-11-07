<?php
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_login();
$pdo = db();

// Mọi truy vấn đều buộc điều kiện quyền người dùng ở phía máy chủ.
$conditions = [];
$params = [];
if ($user['VaiTro'] === 'SINH_VIEN') {
    $conditions[] = 'dd.MaSV = ?';
    $params[] = $user['MaSV'];
} elseif ($user['VaiTro'] === 'GIANG_VIEN') {
    $conditions[] = 'lh.MaGV = ?';
    $params[] = $user['MaGV'];
}

$from = trim((string)($_GET['tu_ngay'] ?? ''));
$to = trim((string)($_GET['den_ngay'] ?? ''));
$class = trim((string)($_GET['lop'] ?? ''));
$errors = [];
if ($from !== '' && !check_date($from)) $errors[] = 'Ngày bắt đầu không hợp lệ.';
if ($to !== '' && !check_date($to)) $errors[] = 'Ngày kết thúc không hợp lệ.';
if (!$errors && $from !== '' && $to !== '' && $to < $from) $errors[] = 'Ngày kết thúc phải bằng hoặc sau ngày bắt đầu.';
if ($class !== '' && check_code($class, 'Mã lớp')) $errors[] = 'Mã lớp không hợp lệ.';

$classesSql = 'SELECT DISTINCT lh.MaLop,l.TenLop FROM LICHHOC lh JOIN LOP l ON l.MaLop=lh.MaLop';
$classesParams = [];
if ($user['VaiTro'] === 'SINH_VIEN') {
    $classesSql .= ' WHERE lh.MaLop=(SELECT MaLop FROM SINHVIEN WHERE MaSV=?)';
    $classesParams[] = $user['MaSV'];
} elseif ($user['VaiTro'] === 'GIANG_VIEN') {
    $classesSql .= ' WHERE lh.MaGV=?';
    $classesParams[] = $user['MaGV'];
}
$classesSql .= ' ORDER BY lh.MaLop';
$st = $pdo->prepare($classesSql);
$st->execute($classesParams);
$classes = $st->fetchAll();
if ($class !== '' && !in_array($class, array_column($classes, 'MaLop'), true)) {
    $errors[] = 'Lớp học không thuộc phạm vi được tra cứu.';
}

if (!$errors) {
    if ($from !== '') { $conditions[] = 'b.NgayHoc >= ?'; $params[] = $from; }
    if ($to !== '') { $conditions[] = 'b.NgayHoc <= ?'; $params[] = $to; }
    if ($class !== '') { $conditions[] = 'lh.MaLop = ?'; $params[] = $class; }
}
$joins = ' FROM DIEMDANH dd JOIN SINHVIEN sv ON sv.MaSV=dd.MaSV '
       . ' JOIN BUOIHOC b ON b.MaBuoi=dd.MaBuoi JOIN LICHHOC lh ON lh.MaLich=b.MaLich';
$where = $conditions ? ' WHERE ' . implode(' AND ', $conditions) : '';
$rows = [];
$counts = array_fill_keys(['Có mặt','Vắng','Đi muộn','Có phép'], 0);
$totalRecords = 0;
if (!$errors) {
    // Thống kê toàn bộ phạm vi lọc, không lấy tỷ lệ sai khi danh sách bị giới hạn 500 hàng.
    $sumSql = 'SELECT dd.TrangThai,COUNT(*) AS SoLuong' . $joins . $where . ' GROUP BY dd.TrangThai';
    $stmt = $pdo->prepare($sumSql);
    $stmt->execute($params);
    foreach ($stmt->fetchAll() as $item) {
        if (array_key_exists($item['TrangThai'], $counts)) {
            $counts[$item['TrangThai']] = (int)$item['SoLuong'];
        }
    }
    $totalRecords = array_sum($counts);
    $sql = 'SELECT dd.MaBuoi,dd.MaSV,sv.HoTen,lh.MaLop,lh.MonHoc,b.NgayHoc,dd.TrangThai,dd.ThoiGian,dd.GhiChu'
         . $joins . $where . ' ORDER BY b.NgayHoc DESC,dd.MaBuoi DESC,dd.MaSV LIMIT 500';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();
}
$ratio = attendance_ratio($counts);
$page_title = 'Lịch sử điểm danh';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><div><h1>Lịch sử điểm danh</h1><p><?= has_role('SINH_VIEN') ? 'Xem các buổi học của bản thân.' : 'Tra cứu theo lớp và khoảng ngày trong phạm vi được phép.' ?></p></div></div>
<?php foreach ($errors as $error): ?><div class="alert error"><?= h($error) ?></div><?php endforeach; ?>
<div class="panel"><form method="get" class="filters">
<div class="field"><label for="lop">Lớp học</label><select id="lop" name="lop"><option value="">Tất cả lớp được xem</option>
<?php foreach ($classes as $item): ?><option value="<?= h($item['MaLop']) ?>" <?= $class === $item['MaLop'] ? 'selected' : '' ?>><?= h($item['MaLop'] . ' – ' . $item['TenLop']) ?></option><?php endforeach; ?>
</select></div>
<div class="field"><label for="tu_ngay">Từ ngày</label><input id="tu_ngay" type="date" name="tu_ngay" value="<?= h($from) ?>"></div>
<div class="field"><label for="den_ngay">Đến ngày</label><input id="den_ngay" type="date" name="den_ngay" value="<?= h($to) ?>"></div>
<button class="btn" type="submit">Lọc kết quả</button><a class="btn btn-light" href="history.php">Xóa lọc</a>
</form></div>
<div class="summary"><span>Tỷ lệ tham gia: <b><?= h(number_format($ratio,1,'.','')) ?>%</b></span>
<span>Có mặt: <b><?= $counts['Có mặt'] ?></b></span><span>Vắng: <b><?= $counts['Vắng'] ?></b></span>
<span>Đi muộn: <b><?= $counts['Đi muộn'] ?></b></span><span>Có phép: <b><?= $counts['Có phép'] ?></b></span>
<span>Tổng bản ghi: <b><?= $totalRecords ?></b></span></div>
<div class="panel scroll"><table class="data-table"><thead><tr><th>Buổi</th><th>Ngày</th><th>Lớp</th><th>Sinh viên</th><th>Môn học</th><th>Trạng thái</th><th>Ghi chú</th></tr></thead><tbody>
<?php foreach ($rows as $item): ?><tr><td><?= h($item['MaBuoi']) ?></td><td><?= h($item['NgayHoc']) ?></td><td><?= h($item['MaLop']) ?></td><td><?= h($item['MaSV'].' – '.$item['HoTen']) ?></td><td><?= h($item['MonHoc']) ?></td><td><span class="badge <?= $item['TrangThai']==='Vắng'?'bad':($item['TrangThai']==='Có mặt'?'good':'warn') ?>"><?= h($item['TrangThai']) ?></span></td><td><?= h($item['GhiChu']) ?></td></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="7" class="empty"><?= $errors ? 'Hãy sửa điều kiện lọc.' : 'Chưa có kết quả phù hợp.' ?></td></tr><?php endif; ?>
</tbody></table><p class="muted-small">Thống kê tính trên toàn bộ bản ghi thỏa điều kiện lọc; bảng hiển thị tối đa 500 kết quả mới nhất. Tỷ lệ tham gia = (Có mặt + Đi muộn) / tổng bản ghi điểm danh × 100%. Có phép nằm trong mẫu số. Đây là cách tính của bản minh họa, không phải quy định chuyên cần chính thức của HaUI.</p></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
