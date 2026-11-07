<?php
require_once __DIR__.'/includes/bootstrap.php';require_role('QUAN_TRI_VIEN');
$pdo=db();$id=trim((string)($_GET['id']??$_POST['original_id']??''));$edit=$id!=='';
$record=['MaGV'=>'','HoTen'=>'','BoMon'=>''];$errors=[];
if($edit){$q=$pdo->prepare('SELECT * FROM GIANGVIEN WHERE MaGV=?');$q->execute([$id]);$record=$q->fetch();if(!$record){flash('error','Không tìm thấy giảng viên.');redirect('teachers.php');}}
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();$record=['MaGV'=>strtoupper(trim((string)($_POST['MaGV']??''))),'HoTen'=>trim((string)($_POST['HoTen']??'')),'BoMon'=>trim((string)($_POST['BoMon']??''))];
 $errors=array_merge(check_code($record['MaGV'],'Mã giảng viên'),check_text($record['HoTen'],'Họ tên'));
 if(text_length($record['BoMon'])>100)$errors[]='Bộ môn không quá 100 ký tự.';
 if(!$errors){try{
  if($edit){$q=$pdo->prepare('UPDATE GIANGVIEN SET HoTen=?,BoMon=? WHERE MaGV=?');$q->execute([$record['HoTen'],$record['BoMon'],$id]);}
  else{$q=$pdo->prepare('INSERT INTO GIANGVIEN(MaGV,HoTen,BoMon) VALUES(?,?,?)');$q->execute(array_values($record));}
  flash('success',$edit?'Đã sửa giảng viên.':'Đã thêm giảng viên.');redirect('teachers.php');
 }catch(PDOException $e){if($e->getCode()==='23000')$errors[]='Mã giảng viên đã tồn tại.';else{error_log($e->getMessage());$errors[]='Không lưu được giảng viên.';}}}
}
$page_title=$edit?'Sửa giảng viên':'Thêm giảng viên';require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1><?=h($page_title)?></h1><p>Quản lý thông tin giảng viên của hệ thống.</p></div><a class="btn btn-light" href="teachers.php">Quay lại</a></div>
<div class="panel panel-narrow"><?php foreach($errors as $e):?><div class="alert error"><?=h($e)?></div><?php endforeach;?>
<form method="post"><?=csrf_field()?><?php if($edit):?><input type="hidden" name="original_id" value="<?=h($id)?>"><?php endif;?>
<div class="field"><label for="code">Mã giảng viên *</label><input id="code" name="MaGV" maxlength="20" required value="<?=h($record['MaGV'])?>" <?=$edit?'readonly':''?>></div>
<div class="field"><label for="name">Họ tên *</label><input id="name" name="HoTen" maxlength="100" required value="<?=h($record['HoTen'])?>"></div>
<div class="field"><label for="bm">Bộ môn</label><input id="bm" name="BoMon" maxlength="100" value="<?=h($record['BoMon'])?>"></div>
<div class="actions"><button class="btn" type="submit">Lưu thông tin</button><a class="btn btn-light" href="teachers.php">Hủy</a></div></form></div>
<?php require __DIR__.'/includes/footer.php'; ?>