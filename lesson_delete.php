<?php
require_once __DIR__.'/includes/bootstrap.php';$user=require_role('QUAN_TRI_VIEN','GIANG_VIEN');require_post();verify_csrf();
$id=trim((string)($_POST['id']??''));if($id===''){flash('error','Thiếu mã buổi học.');redirect('lessons.php');}
$check=db()->prepare('SELECT lh.MaGV FROM BUOIHOC b JOIN LICHHOC lh ON lh.MaLich=b.MaLich WHERE b.MaBuoi=?');$check->execute([$id]);$gv=$check->fetchColumn();
if(!$gv){flash('error','Không tìm thấy buổi học.');redirect('lessons.php');}
if($user['VaiTro']==='GIANG_VIEN' && $gv!==$user['MaGV']){http_response_code(403);exit('Không có quyền xóa buổi học này.');}
try{$q=db()->prepare('DELETE FROM BUOIHOC WHERE MaBuoi=?');$q->execute([$id]);flash('success','Đã xóa buổi học.');}
catch(PDOException $e){if($e->getCode()==='23000')flash('error','Buổi học đã có điểm danh hoặc thông báo, không thể xóa.');else{error_log($e->getMessage());flash('error','Không xóa được buổi học.');}}
redirect('lessons.php');