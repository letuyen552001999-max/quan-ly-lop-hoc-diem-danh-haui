<?php
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_login();
$pdo = db();
$sql = 'SELECT l.MaLop, l.TenLop, l.Khoa, COUNT(s.MaSV) AS SoSV FROM LOP l LEFT JOIN SINHVIEN s ON l.MaLop=s.MaLop';
$params = [];
if ($user['VaiTro'] === 'GIANG_VIEN') {
    $sql .= ' WHERE EXISTS (SELECT 1 FROM LICHHOC lh WHERE lh.MaLop=l.MaLop AND lh.MaGV=?)';
    $params[] = $user['MaGV'];
} elseif ($user['VaiTro'] === 'SINH_VIEN') {
    $sql .= ' WHERE EXISTS (SELECT 1 FROM SINHVIEN sv WHERE sv.MaLop=l.MaLop AND sv.MaSV=?)';
    $params[] = $user['MaSV'];
}
$sql .= ' GROUP BY l.MaLop,l.TenLop,l.Khoa ORDER BY l.MaLop';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();
$page_title = 'Danh sách lớp';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head"><div><h1>Quản lý lớp học</h1><p>Các lớp được phép xem theo vai trò người dùng.</p></div><?php if(has_role('QUAN_TRI_VIEN')):?><a class="btn" href="class_form.php">+ Thêm lớp</a><?php endif;?></div>
<div class="panel scroll"><table class="data-table"><thead><tr><th>Mã lớp</th><th>Tên lớp</th><th>Khóa</th><th>Số sinh viên</th><?php if(has_role('QUAN_TRI_VIEN')):?><th>Thao tác</th><?php endif;?></tr></thead><tbody>
<?php foreach ($rows as $row): ?><tr><td><?= h($row['MaLop']) ?></td><td><?= h($row['TenLop']) ?></td><td><?= h($row['Khoa']) ?></td><td><?= h((string)$row['SoSV']) ?></td><?php if(has_role('QUAN_TRI_VIEN')):?><td><div class="actions"><a class="btn btn-sm btn-light" href="class_form.php?id=<?=rawurlencode($row['MaLop'])?>">Sửa</a><form method="post" action="class_delete.php" onsubmit="return confirm('Bạn muốn xóa lớp này?');"><?=csrf_field()?><input type="hidden" name="id" value="<?=h($row['MaLop'])?>"><button class="btn btn-sm btn-danger">Xóa</button></form></div></td><?php endif;?></tr><?php endforeach; ?>
<?php if (!$rows): ?><tr><td colspan="<?=has_role('QUAN_TRI_VIEN')?5:4?>" class="empty">Chưa có lớp học được phân quyền.</td></tr><?php endif; ?>
</tbody></table></div>
<?php require __DIR__ . '/includes/footer.php'; ?>
