<?php
require_once __DIR__.'/includes/bootstrap.php';
$user=require_role('SINH_VIEN');
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf(); require_post();
    $id=filter_var($_POST['id']??'',FILTER_VALIDATE_INT);
    if(!$id){flash('error','Thông báo không hợp lệ.');redirect('notifications.php');}
    $stmt=db()->prepare('UPDATE THONGBAO SET DaXem=1 WHERE MaTB=? AND MaSV=?');
    $stmt->execute([$id,$user['MaSV']]);
    flash($stmt->rowCount()?'success':'error',$stmt->rowCount()?'Đã đánh dấu đã đọc.':'Không tìm thấy thông báo chưa đọc.');
    redirect('notifications.php');
}
$stmt=db()->prepare('SELECT MaTB,MaBuoi,NoiDung,DaXem,ThoiDiem FROM THONGBAO WHERE MaSV=? ORDER BY ThoiDiem DESC,MaTB DESC LIMIT 200');
$stmt->execute([$user['MaSV']]); $rows=$stmt->fetchAll();
$page_title='Thông báo'; require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1>Thông báo điểm danh</h1><p>Chỉ hiển thị các thông báo gửi đến tài khoản của bạn.</p></div></div>
<div class="panel scroll"><table class="data-table"><thead><tr><th>Thời điểm</th><th>Nội dung</th><th>Trạng thái</th><th>Thao tác</th></tr></thead><tbody>
<?php foreach($rows as $r): ?><tr><td><?= h($r['ThoiDiem']) ?></td><td><?= h($r['NoiDung']) ?></td><td><span class="badge <?= $r['DaXem']?'':'warn' ?>"><?= $r['DaXem']?'Đã đọc':'Chưa đọc' ?></span></td><td><?php if(!$r['DaXem']): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$r['MaTB'] ?>"><button class="btn btn-sm btn-light" type="submit">Đánh dấu đã đọc</button></form><?php endif; ?></td></tr><?php endforeach; ?>
<?php if(!$rows): ?><tr><td colspan="4" class="empty">Chưa có thông báo.</td></tr><?php endif; ?>
</tbody></table></div>
<?php require __DIR__.'/includes/footer.php'; ?>
