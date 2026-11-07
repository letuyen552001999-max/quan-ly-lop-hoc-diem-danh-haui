<?php
require_once __DIR__.'/includes/bootstrap.php';
require_role('QUAN_TRI_VIEN');
$pdo=db(); $id=trim((string)($_GET['id']??$_POST['original_id']??'')); $edit=$id!=='';
$record=['MaLop'=>'','TenLop'=>'','Khoa'=>'']; $errors=[];
if($edit){$q=$pdo->prepare('SELECT * FROM LOP WHERE MaLop=?');$q->execute([$id]);$record=$q->fetch();if(!$record){flash('error','Không tìm thấy lớp.');redirect('classes.php');}}
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $record=['MaLop'=>strtoupper(trim((string)($_POST['MaLop']??''))), 'TenLop'=>trim((string)($_POST['TenLop']??'')), 'Khoa'=>trim((string)($_POST['Khoa']??''))];
 $errors=array_merge(check_code($record['MaLop'],'Mã lớp'),check_text($record['TenLop'],'Tên lớp'),check_text($record['Khoa'],'Khóa học',10));
 if(!$errors){try{
  if($edit){$q=$pdo->prepare('UPDATE LOP SET TenLop=?,Khoa=? WHERE MaLop=?');$q->execute([$record['TenLop'],$record['Khoa'],$id]);}
  else{$q=$pdo->prepare('INSERT INTO LOP(MaLop,TenLop,Khoa) VALUES(?,?,?)');$q->execute(array_values($record));}
  flash('success',$edit?'Đã sửa thông tin lớp.':'Đã thêm lớp mới.');redirect('classes.php');
 }catch(PDOException $e){if($e->getCode()==='23000')$errors[]='Mã lớp đã có trong hệ thống.';else{error_log($e->getMessage());$errors[]='Không lưu được thông tin lớp.';}}}
}
$page_title=$edit?'Sửa lớp':'Thêm lớp'; require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1><?=h($page_title)?></h1><p>Quản trị viên cập nhật thông tin lớp học.</p></div><a class="btn btn-light" href="classes.php">Quay lại</a></div>
<div class="panel panel-narrow"><?php foreach($errors as $e):?><div class="alert error"><?=h($e)?></div><?php endforeach;?>
<form method="post"><?=csrf_field()?><?php if($edit):?><input type="hidden" name="original_id" value="<?=h($id)?>"><?php endif;?>
<div class="field"><label for="code">Mã lớp *</label><input id="code" name="MaLop" maxlength="20" required value="<?=h($record['MaLop'])?>" <?=$edit?'readonly':''?>></div>
<div class="field"><label for="name">Tên lớp *</label><input id="name" name="TenLop" maxlength="100" required value="<?=h($record['TenLop'])?>"></div>
<div class="field"><label for="khoa">Khóa *</label><input id="khoa" name="Khoa" maxlength="10" required value="<?=h($record['Khoa'])?>" placeholder="K19"></div>
<div class="actions"><button class="btn" type="submit">Lưu thông tin</button><a href="classes.php" class="btn btn-light">Hủy</a></div></form></div>
<?php require __DIR__.'/includes/footer.php'; ?>