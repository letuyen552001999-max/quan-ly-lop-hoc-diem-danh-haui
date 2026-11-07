<?php
require_once __DIR__.'/includes/bootstrap.php';require_role('QUAN_TRI_VIEN');require_post();verify_csrf();
$id=trim((string)($_POST['id']??''));if($id===''){flash('error','Thiếu mã giảng viên.');redirect('teachers.php');}
try{$q=db()->prepare('DELETE FROM GIANGVIEN WHERE MaGV=?');$q->execute([$id]);flash($q->rowCount()?'success':'error',$q->rowCount()?'Đã xóa giảng viên.':'Không tìm thấy giảng viên.');}
catch(PDOException $e){if($e->getCode()==='23000')flash('error','Không thể xóa giảng viên đã có lịch học hoặc tài khoản.');else{error_log($e->getMessage());flash('error','Không xóa được giảng viên.');}}
redirect('teachers.php');