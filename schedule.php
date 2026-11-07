<?php
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_login();
$sql = 'SELECT lh.MaLich,lh.MaLop,l.TenLop,lh.MaGV,g.HoTen AS TenGV,lh.MonHoc,lh.Thu,lh.GioBatDau,lh.GioKetThuc,lh.Phong FROM LICHHOC lh JOIN LOP l ON l.MaLop=lh.MaLop JOIN GIANGVIEN g ON g.MaGV=lh.MaGV';
$params = [];
if ($user['VaiTro'] === 'GIANG_VIEN') {
    $sql .= ' WHERE lh.MaGV=?'; $params[] = $user['MaGV'];
} elseif ($user['VaiTro'] === 'SINH_VIEN') {
    $sql .= ' WHERE lh.MaLop=(SELECT MaLop FROM SINHVIEN WHERE MaSV=?)'; $params[] = $user['MaSV'];
}
$sql .= ' ORDER BY lh.Thu,lh.GioBatDau,lh.MaLop';
$stmt=db()->prepare($sql); $stmt->execute($params); $rows=$stmt->fetchAll();
$page_title='Lịch học'; require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1>Lịch học</h1><p>Thông tin lịch học được lưu trong cơ sở dữ liệu.</p></div><?php if(has_role('QUAN_TRI_VIEN')):?><a class="btn" href="schedule_form.php">+ Thêm lịch học</a><?php endif;?></div>
<div class="panel scroll"><table class="data-table"><thead><tr><th>Lớp</th><th>Môn học</th><th>Giảng viên</th><th>Thứ</th><th>Giờ học</th><th>Phòng</th><?php if(has_role('QUAN_TRI_VIEN')):?><th>Thao tác</th><?php endif;?></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= h($r['TenLop']) ?></td><td><?= h($r['MonHoc']) ?></td><td><?= h($r['TenGV']) ?></td><td><?= h((string)$r['Thu']) ?></td><td><?= h(substr($r['GioBatDau'],0,5).' – '.substr($r['GioKetThuc'],0,5)) ?></td><td><?= h($r['Phong']) ?></td><?php if(has_role('QUAN_TRI_VIEN')):?><td><div class="actions"><a class="btn btn-sm btn-light" href="schedule_form.php?id=<?=(int)$r['MaLich']?>">Sửa</a><form method="post" action="schedule_delete.php" onsubmit="return confirm('Xóa lịch học này?');"><?=csrf_field()?><input type="hidden" name="id" value="<?=(int)$r['MaLich']?>"><button class="btn btn-sm btn-danger">Xóa</button></form></div></td><?php endif;?></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="<?=has_role('QUAN_TRI_VIEN')?7:6?>" class="empty">Chưa có lịch học.</td></tr><?php endif; ?>
</tbody></table></div>
<?php require __DIR__.'/includes/footer.php'; ?>
