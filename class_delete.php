<?php
require_once __DIR__.'/includes/bootstrap.php';require_role('QUAN_TRI_VIEN');require_post();verify_csrf();
$id=trim((string)($_POST['id']??''));
if($id===''){flash('error','Thiếu mã lớp.');redirect('classes.php');}
try{$q=db()->prepare('DELETE FROM LOP WHERE MaLop=?');$q->execute([$id]);flash($q->rowCount()?'success':'error',$q->rowCount()?'Đã xóa lớp.':'Không tìm thấy lớp.');}
catch(PDOException $e){if($e->getCode()==='23000')flash('error','Không thể xóa lớp đã có sinh viên hoặc lịch học.');else{error_log($e->getMessage());flash('error','Không xóa được lớp.');}}
redirect('classes.php');