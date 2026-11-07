<?php
require_once __DIR__.'/includes/bootstrap.php';require_role('QUAN_TRI_VIEN');
$q=trim((string)($_GET['q']??''));$sql='SELECT g.MaGV,g.HoTen,g.BoMon,COUNT(lh.MaLich) SoLich FROM GIANGVIEN g LEFT JOIN LICHHOC lh ON lh.MaGV=g.MaGV';$params=[];
if($q!==''){$sql.=' WHERE g.MaGV LIKE ? OR g.HoTen LIKE ?';$params=["%$q%","%$q%"];} $sql.=' GROUP BY g.MaGV,g.HoTen,g.BoMon ORDER BY g.MaGV LIMIT 300';
$stmt=db()->prepare($sql);$stmt->execute($params);$rows=$stmt->fetchAll();$page_title='Quản lý giảng viên';require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1>Quản lý giảng viên</h1><p>Danh sách giảng viên và lịch học phụ trách.</p></div><a class="btn" href="teacher_form.php">+ Thêm giảng viên</a></div>
<div class="panel"><form class="filters" method="get"><div class="field"><label for="q">Mã hoặc họ tên</label><input id="q" name="q" value="<?=h($q)?>" placeholder="Tìm giảng viên"></div><button class="btn">Tìm kiếm</button><a class="btn btn-light" href="teachers.php">Xóa lọc</a></form></div>
<div class="panel scroll"><table class="data-table"><thead><tr><th>Mã GV</th><th>Họ tên</th><th>Bộ môn</th><th>Số lịch</th><th>Thao tác</th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=h($r['MaGV'])?></td><td><?=h($r['HoTen'])?></td><td><?=h($r['BoMon'])?></td><td><?=h((string)$r['SoLich'])?></td><td><div class="actions"><a class="btn btn-sm btn-light" href="teacher_form.php?id=<?=rawurlencode($r['MaGV'])?>">Sửa</a><form method="post" action="teacher_delete.php" onsubmit="return confirm('Bạn muốn xóa giảng viên này?');"><?=csrf_field()?><input type="hidden" name="id" value="<?=h($r['MaGV'])?>"><button class="btn btn-sm btn-danger">Xóa</button></form></div></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="5" class="empty">Chưa có giảng viên.</td></tr><?php endif;?></tbody></table></div>
<?php require __DIR__.'/includes/footer.php'; ?>