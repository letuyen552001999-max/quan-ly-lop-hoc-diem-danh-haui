<?php
require_once __DIR__.'/includes/bootstrap.php';require_role('QUAN_TRI_VIEN');
$pdo=db();$id=(int)($_GET['id']??$_POST['original_id']??0);$edit=$id>0;
$record=['MaLop'=>'','MaGV'=>'','MonHoc'=>'','Thu'=>'2','GioBatDau'=>'08:00','GioKetThuc'=>'10:00','Phong'=>''];$errors=[];
if($edit){$q=$pdo->prepare('SELECT * FROM LICHHOC WHERE MaLich=?');$q->execute([$id]);$record=$q->fetch();$originalClass=$record['MaLop']??'';$originalTeacher=$record['MaGV']??'';if(!$record){flash('error','Không tìm thấy lịch học.');redirect('schedule.php');}}
$classes=$pdo->query('SELECT MaLop,TenLop FROM LOP ORDER BY MaLop')->fetchAll();$teachers=$pdo->query('SELECT MaGV,HoTen FROM GIANGVIEN ORDER BY MaGV')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$record=['MaLop'=>trim((string)($_POST['MaLop']??'')),'MaGV'=>trim((string)($_POST['MaGV']??'')),'MonHoc'=>trim((string)($_POST['MonHoc']??'')),'Thu'=>(string)($_POST['Thu']??''),'GioBatDau'=>trim((string)($_POST['GioBatDau']??'')),'GioKetThuc'=>trim((string)($_POST['GioKetThuc']??'')),'Phong'=>trim((string)($_POST['Phong']??''))];
 $errors=check_text($record['MonHoc'],'Môn học');
 if(!in_array($record['MaLop'],array_column($classes,'MaLop'),true))$errors[]='Chọn lớp hợp lệ.';
 if(!in_array($record['MaGV'],array_column($teachers,'MaGV'),true))$errors[]='Chọn giảng viên hợp lệ.';
 if(!ctype_digit($record['Thu'])||!check_weekday((int)$record['Thu']))$errors[]='Thứ trong tuần từ 2 đến 8 (8 là Chủ nhật).';
 if(!check_hours($record['GioBatDau'],$record['GioKetThuc']))$errors[]='Giờ bắt đầu phải trước giờ kết thúc, định dạng HH:MM.';
 if(text_length($record['Phong'])>50)$errors[]='Phòng học không quá 50 ký tự.';
 if(!$errors){try{
  if($edit && ($record['MaLop']!==($originalClass??'') || $record['MaGV']!==($originalTeacher??''))){
    $q=$pdo->prepare('SELECT COUNT(*) FROM DIEMDANH dd JOIN BUOIHOC b ON b.MaBuoi=dd.MaBuoi WHERE b.MaLich=?');$q->execute([$id]);
    if((int)$q->fetchColumn()>0)$errors[]='Lịch đã có dữ liệu điểm danh, không thể đổi lớp hoặc giảng viên phụ trách.';
  }
  if($errors)throw new RuntimeException('validation');
  if($edit){$q=$pdo->prepare('UPDATE LICHHOC SET MaLop=?,MaGV=?,MonHoc=?,Thu=?,GioBatDau=?,GioKetThuc=?,Phong=? WHERE MaLich=?');$q->execute([...array_values($record),$id]);}
  else{$q=$pdo->prepare('INSERT INTO LICHHOC(MaLop,MaGV,MonHoc,Thu,GioBatDau,GioKetThuc,Phong) VALUES (?,?,?,?,?,?,?)');$q->execute(array_values($record));}
  flash('success',$edit?'Đã cập nhật lịch học.':'Đã thêm lịch học.');redirect('schedule.php');
 }catch(PDOException $e){error_log($e->getMessage());$errors[]='Không lưu được lịch học, hãy kiểm tra dữ liệu.';}catch(RuntimeException $e){/* Giữ thông báo kiểm tra ở trên. */}}
}
$page_title=$edit?'Sửa lịch học':'Thêm lịch học';require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1><?=h($page_title)?></h1><p>Tạo lịch học cho lớp và giảng viên.</p></div><a class="btn btn-light" href="schedule.php">Quay lại</a></div>
<div class="panel panel-narrow"><?php foreach($errors as $e):?><div class="alert error"><?=h($e)?></div><?php endforeach;?>
<form method="post"><?=csrf_field()?><?php if($edit):?><input type="hidden" name="original_id" value="<?=h((string)$id)?>"><?php endif;?>
<div class="grid-2"><div class="field"><label for="lop">Lớp *</label><select id="lop" name="MaLop" required><option value="">-- Chọn lớp --</option><?php foreach($classes as $r):?><option value="<?=h($r['MaLop'])?>" <?=$record['MaLop']===$r['MaLop']?'selected':''?>><?=h($r['TenLop'])?></option><?php endforeach;?></select></div>
<div class="field"><label for="gv">Giảng viên *</label><select id="gv" name="MaGV" required><option value="">-- Chọn giảng viên --</option><?php foreach($teachers as $r):?><option value="<?=h($r['MaGV'])?>" <?=$record['MaGV']===$r['MaGV']?'selected':''?>><?=h($r['HoTen'])?></option><?php endforeach;?></select></div></div>
<div class="field"><label for="mon">Môn học *</label><input name="MonHoc" id="mon" maxlength="100" required value="<?=h($record['MonHoc'])?>"></div>
<div class="grid-2"><div class="field"><label for="thu">Thứ *</label><select name="Thu" id="thu"><?php for($i=2;$i<=8;$i++):?><option value="<?=$i?>" <?=(int)$record['Thu']===$i?'selected':''?>><?=$i===8?'Chủ nhật':'Thứ '.$i?></option><?php endfor;?></select></div><div class="field"><label for="phong">Phòng học</label><input name="Phong" id="phong" maxlength="50" value="<?=h($record['Phong'])?>"></div></div>
<div class="grid-2"><div class="field"><label for="start">Giờ bắt đầu *</label><input type="time" name="GioBatDau" id="start" required value="<?=h(substr($record['GioBatDau'],0,5))?>"></div><div class="field"><label for="end">Giờ kết thúc *</label><input type="time" name="GioKetThuc" id="end" required value="<?=h(substr($record['GioKetThuc'],0,5))?>"></div></div>
<div class="actions"><button class="btn">Lưu thông tin</button><a class="btn btn-light" href="schedule.php">Hủy</a></div></form></div>
<?php require __DIR__.'/includes/footer.php'; ?>