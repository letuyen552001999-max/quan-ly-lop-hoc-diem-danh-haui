<?php
require_once __DIR__.'/includes/bootstrap.php';$user=require_role('QUAN_TRI_VIEN','GIANG_VIEN');
$pdo=db();$id=trim((string)($_GET['id']??$_POST['original_id']??''));$edit=$id!=='';
$record=['MaBuoi'=>'','MaLich'=>'','NgayHoc'=>date('Y-m-d'),'NoiDung'=>''];$errors=[];
$sql='SELECT lh.MaLich,lh.MaLop,lh.MaGV,lh.MonHoc,l.TenLop FROM LICHHOC lh JOIN LOP l ON l.MaLop=lh.MaLop';$params=[];
if($user['VaiTro']==='GIANG_VIEN'){$sql.=' WHERE lh.MaGV=?';$params[]=$user['MaGV'];}
$sql.=' ORDER BY lh.MaLich';$st=$pdo->prepare($sql);$st->execute($params);$lists=$st->fetchAll();
if($edit){$q=$pdo->prepare('SELECT * FROM BUOIHOC WHERE MaBuoi=?');$q->execute([$id]);$record=$q->fetch();$originalSchedule=(string)($record['MaLich']??'');if(!$record){flash('error','Không tìm thấy buổi học.');redirect('lessons.php');}
 if(!in_array((string)$record['MaLich'],array_map(fn($r)=>(string)$r['MaLich'],$lists),true)){http_response_code(403);exit('Bạn không được sửa buổi học này.');}}
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$record=['MaBuoi'=>strtoupper(trim((string)($_POST['MaBuoi']??''))),'MaLich'=>(string)($_POST['MaLich']??''),'NgayHoc'=>trim((string)($_POST['NgayHoc']??'')),'NoiDung'=>trim((string)($_POST['NoiDung']??''))];
 $errors=check_code($record['MaBuoi'],'Mã buổi học');
 if(!in_array($record['MaLich'],array_map(fn($r)=>(string)$r['MaLich'],$lists),true))$errors[]='Chọn lịch học được phân công.';
 if(!check_date($record['NgayHoc']))$errors[]='Ngày học không hợp lệ.';
 if(text_length($record['NoiDung'])>255)$errors[]='Nội dung không quá 255 ký tự.';
 if(!$errors){try{
  // Tránh chuyển buổi đã điểm danh sang lớp khác, gây sai dữ liệu lịch sử.
  if($edit && (string)$record['MaLich']!== ($originalSchedule??'')){
   $q=$pdo->prepare('SELECT COUNT(*) FROM DIEMDANH WHERE MaBuoi=?');$q->execute([$id]);if((int)$q->fetchColumn()>0)$errors[]='Buổi đã điểm danh, không thể chuyển sang lịch học khác.';
  }
  if(!$errors){
   if($edit){$q=$pdo->prepare('UPDATE BUOIHOC SET MaLich=?,NgayHoc=?,NoiDung=? WHERE MaBuoi=?');$q->execute([$record['MaLich'],$record['NgayHoc'],$record['NoiDung'],$id]);}
   else{$q=$pdo->prepare('INSERT INTO BUOIHOC(MaBuoi,MaLich,NgayHoc,NoiDung) VALUES(?,?,?,?)');$q->execute(array_values($record));}
   flash('success',$edit?'Đã cập nhật buổi học.':'Đã tạo buổi học.');redirect('lessons.php');
  }
 }catch(PDOException $e){if($e->getCode()==='23000')$errors[]='Mã buổi học đã tồn tại hoặc thiếu lịch học.';else{error_log($e->getMessage());$errors[]='Không lưu được buổi học.';}}}
}
$page_title=$edit?'Sửa buổi học':'Tạo buổi học';require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1><?=h($page_title)?></h1><p>Chọn lịch được phân công và ngày tổ chức buổi học.</p></div><a href="lessons.php" class="btn btn-light">Quay lại</a></div>
<div class="panel panel-narrow"><?php foreach($errors as $e):?><div class="alert error"><?=h($e)?></div><?php endforeach;?>
<form method="post"><?=csrf_field()?><?php if($edit):?><input type="hidden" name="original_id" value="<?=h($id)?>"><?php endif;?>
<div class="field"><label for="code">Mã buổi học *</label><input id="code" name="MaBuoi" maxlength="20" required value="<?=h($record['MaBuoi'])?>" <?=$edit?'readonly':''?> placeholder="B002"></div>
<div class="field"><label for="lich">Lịch học *</label><select id="lich" name="MaLich" required><option value="">-- Chọn lịch học --</option><?php foreach($lists as $r):?><option value="<?=h((string)$r['MaLich'])?>" <?=(string)$record['MaLich']===(string)$r['MaLich']?'selected':''?>><?=h($r['TenLop'].' · '.$r['MonHoc'].' ('.$r['MaLich'].')')?></option><?php endforeach;?></select></div>
<div class="field"><label for="ngay">Ngày học *</label><input type="date" id="ngay" name="NgayHoc" value="<?=h($record['NgayHoc'])?>" required></div>
<div class="field"><label for="nd">Nội dung</label><textarea name="NoiDung" id="nd" maxlength="255" rows="3"><?=h($record['NoiDung'])?></textarea></div>
<div class="actions"><button class="btn">Lưu buổi học</button><a href="lessons.php" class="btn btn-light">Hủy</a></div></form></div>
<?php require __DIR__.'/includes/footer.php'; ?>