<?php
require_once __DIR__.'/includes/bootstrap.php';$user=require_role('QUAN_TRI_VIEN','GIANG_VIEN');
$sql='SELECT b.MaBuoi,b.NgayHoc,b.NoiDung,lh.MaGV,lh.MonHoc,l.TenLop,(SELECT COUNT(*) FROM DIEMDANH dd WHERE dd.MaBuoi=b.MaBuoi) SoDaDiemDanh FROM BUOIHOC b JOIN LICHHOC lh ON lh.MaLich=b.MaLich JOIN LOP l ON l.MaLop=lh.MaLop';$params=[];
if($user['VaiTro']==='GIANG_VIEN'){$sql.=' WHERE lh.MaGV=?';$params[]=$user['MaGV'];}$sql.=' ORDER BY b.NgayHoc DESC,b.MaBuoi LIMIT 300';
$q=db()->prepare($sql);$q->execute($params);$rows=$q->fetchAll();$page_title='Quản lý buổi học';require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1>Quản lý buổi học</h1><p>Tạo buổi học trước khi thực hiện điểm danh.</p></div><a class="btn" href="lesson_form.php">+ Tạo buổi học</a></div>
<div class="panel scroll"><table class="data-table"><thead><tr><th>Mã buổi</th><th>Ngày học</th><th>Lớp</th><th>Môn học</th><th>Đã điểm danh</th><th>Thao tác</th></tr></thead><tbody>
<?php foreach($rows as $r):?><tr><td><?=h($r['MaBuoi'])?></td><td><?=h($r['NgayHoc'])?></td><td><?=h($r['TenLop'])?></td><td><?=h($r['MonHoc'])?></td><td><?=h((string)$r['SoDaDiemDanh'])?></td><td><div class="actions"><a href="lesson_form.php?id=<?=rawurlencode($r['MaBuoi'])?>" class="btn btn-sm btn-light">Sửa</a><a href="attendance.php?buoi=<?=rawurlencode($r['MaBuoi'])?>" class="btn btn-sm btn-light">Điểm danh</a><form method="post" action="lesson_delete.php" onsubmit="return confirm('Xóa buổi học chưa có dữ liệu điểm danh?');"><?=csrf_field()?><input type="hidden" name="id" value="<?=h($r['MaBuoi'])?>"><button class="btn btn-sm btn-danger">Xóa</button></form></div></td></tr><?php endforeach;?>
<?php if(!$rows):?><tr><td colspan="6" class="empty">Chưa có buổi học trong phạm vi phụ trách.</td></tr><?php endif;?></tbody></table></div>
<?php require __DIR__.'/includes/footer.php'; ?>