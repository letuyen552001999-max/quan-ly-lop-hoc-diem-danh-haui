<?php
require_once __DIR__.'/includes/bootstrap.php';require_role('QUAN_TRI_VIEN');require_post();verify_csrf();
$id=filter_var($_POST['id']??'',FILTER_VALIDATE_INT);if(!$id){flash('error','Mã lịch không hợp lệ.');redirect('schedule.php');}
try{$q=db()->prepare('DELETE FROM LICHHOC WHERE MaLich=?');$q->execute([$id]);flash($q->rowCount()?'success':'error',$q->rowCount()?'Đã xóa lịch học.':'Không tìm thấy lịch học.');}
catch(PDOException $e){if($e->getCode()==='23000')flash('error','Không thể xóa lịch học đã có buổi học.');else{error_log($e->getMessage());flash('error','Không xóa được lịch học.');}}
redirect('schedule.php');