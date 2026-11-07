<?php
require_once __DIR__ . '/includes/bootstrap.php';
$user = require_role('GIANG_VIEN', 'QUAN_TRI_VIEN');
$pdo=db();
// Giảng viên chỉ được xem và điểm danh các buổi của mình.
$where = $user['VaiTro'] === 'GIANG_VIEN' ? ' WHERE lh.MaGV=?' : '';
$parameters = $user['VaiTro'] === 'GIANG_VIEN' ? [$user['MaGV']] : [];
$sql = 'SELECT b.MaBuoi,b.NgayHoc,b.NoiDung,lh.MaLop,lh.MaGV,lh.MonHoc,l.TenLop FROM BUOIHOC b JOIN LICHHOC lh ON lh.MaLich=b.MaLich JOIN LOP l ON l.MaLop=lh.MaLop'.$where.' ORDER BY b.NgayHoc DESC,b.MaBuoi';
$stmt=$pdo->prepare($sql);$stmt->execute($parameters);$lessons=$stmt->fetchAll();
$selected=trim((string)($_GET['buoi'] ?? $_POST['buoi'] ?? ''));
$lesson=null;
foreach($lessons as $r) if($r['MaBuoi']===$selected) {$lesson=$r;break;}
$errors=[];
$statuses=['Có mặt','Vắng','Đi muộn','Có phép'];
$students=[];$records=[];
if($lesson){
    $stmt=$pdo->prepare('SELECT MaSV,HoTen FROM SINHVIEN WHERE MaLop=? ORDER BY MaSV');
    $stmt->execute([$lesson['MaLop']]); $students=$stmt->fetchAll();
    $stmt=$pdo->prepare('SELECT MaSV,TrangThai,GhiChu FROM DIEMDANH WHERE MaBuoi=?');
    $stmt->execute([$selected]);
    foreach($stmt->fetchAll() as $rec) $records[$rec['MaSV']]=$rec;
}
if($_SERVER['REQUEST_METHOD']==='POST'){
    require_post();verify_csrf();
    if($user['VaiTro']!=='GIANG_VIEN'){
        http_response_code(403);exit('Chỉ giảng viên phụ trách được ghi kết quả điểm danh.');
    }
    if(!$lesson){
        $errors[]='Buổi học không tồn tại hoặc bạn không phụ trách buổi học này.';
    } else {
        $postedStatuses = $_POST['status'] ?? [];
        $notes = $_POST['note'] ?? [];
        if (!is_array($postedStatuses) || !is_array($notes)) {
            $errors[]='Dữ liệu điểm danh không hợp lệ.';
        } else {
            $validStudents=array_column($students,'MaSV');
            if(count($validStudents)===0){
                $errors[]='Lớp chưa có sinh viên.';
            } elseif(array_diff($validStudents,array_keys($postedStatuses)) || array_diff(array_keys($postedStatuses),$validStudents)) {
                $errors[]='Danh sách điểm danh không khớp với sinh viên của lớp.';
            } else {
                foreach($validStudents as $masv){
                    if(!is_string($postedStatuses[$masv]) || !in_array($postedStatuses[$masv],$statuses,true)){
                        $errors[]='Trạng thái của sinh viên '.$masv.' không hợp lệ.';break;
                    }
                    if(isset($notes[$masv]) && (!is_string($notes[$masv]) || text_length(trim($notes[$masv]))>255)){
                        $errors[]='Ghi chú của sinh viên '.$masv.' quá dài.';break;
                    }
                }
            }
        }
    }
    if(!$errors){
        try{
            $pdo->beginTransaction();
            // Khóa bản ghi lớp để thao tác sửa lớp không chen ngang khi chấm điểm.
            $lock=$pdo->prepare('SELECT MaSV FROM SINHVIEN WHERE MaLop=? ORDER BY MaSV FOR UPDATE');
            $lock->execute([$lesson['MaLop']]);
            $actual=$lock->fetchAll(PDO::FETCH_COLUMN);
            if($actual !== $validStudents) throw new RuntimeException('Danh sách sinh viên đã thay đổi. Hãy tải lại trang.');
            // Đọc trạng thái cũ bên trong giao dịch để so sánh đúng trước khi phát thông báo.
            $previousRows=$pdo->prepare('SELECT MaSV,TrangThai,GhiChu FROM DIEMDANH WHERE MaBuoi=? FOR UPDATE');
            $previousRows->execute([$selected]);
            $currentRecords=[];
            foreach($previousRows->fetchAll() as $row) $currentRecords[$row['MaSV']]=$row;
            $up=$pdo->prepare('INSERT INTO DIEMDANH (MaBuoi,MaSV,TrangThai,ThoiGian,GhiChu) VALUES (?,?,?,CURRENT_TIME(),?) ON DUPLICATE KEY UPDATE TrangThai=VALUES(TrangThai),ThoiGian=VALUES(ThoiGian),GhiChu=VALUES(GhiChu)');
            $notify=$pdo->prepare('INSERT INTO THONGBAO (MaSV,MaBuoi,NoiDung) VALUES (?,?,?)');
            foreach($students as $sv){
                $code=$sv['MaSV'];
                $status=$postedStatuses[$code];
                $note=trim((string)($notes[$code]??''));
                $previous=$currentRecords[$code]['TrangThai'] ?? null;
                if($previous===$status && ($currentRecords[$code]['GhiChu']??'')===$note) continue;
                $up->execute([$selected,$code,$status,$note]);
                if($previous!==$status){
                    $notify->execute([$code,$selected,'Điểm danh buổi '.$selected.' đã cập nhật: '.$status.'.']);
                }
            }
            $pdo->commit();
            flash('success','Đã lưu điểm danh buổi '.$selected.'.');
            redirect('attendance.php?buoi='.rawurlencode($selected));
        } catch(Throwable $e){
            if($pdo->inTransaction())$pdo->rollBack();
            error_log($e->getMessage());
            $errors[]='Không lưu được điểm danh: danh sách đã thay đổi hoặc CSDL gặp lỗi. Hãy tải lại trang.';
        }
    }
}
$page_title='Điểm danh sinh viên'; require __DIR__.'/includes/header.php';
?>
<div class="page-head"><div><h1>Điểm danh sinh viên</h1><p>Giảng viên chọn buổi học rồi ghi trạng thái theo từng sinh viên.</p></div></div>
<?php foreach($errors as $err): ?><div class="alert error"><?= h($err) ?></div><?php endforeach; ?>
<div class="panel"><form method="get" class="filters"><div class="field"><label for="buoi">Chọn buổi học</label><select name="buoi" id="buoi" required><option value="">-- Chọn buổi học --</option>
<?php foreach($lessons as $b): ?><option value="<?= h($b['MaBuoi']) ?>" <?= $b['MaBuoi']===$selected?'selected':'' ?>><?= h($b['MaBuoi'].' | '.$b['NgayHoc'].' | '.$b['TenLop'].' | '.$b['MonHoc']) ?></option><?php endforeach; ?>
</select></div><button class="btn" type="submit">Xem danh sách</button></form>
<?php if(!$lessons): ?><p class="muted">Chưa có buổi học được phân công. Dùng dữ liệu mẫu B001 hoặc bổ sung buổi học vào CSDL.</p><?php endif; ?></div>
<?php if($lesson): ?>
<div class="panel"><h2><?= h($lesson['TenLop']) ?> – <?= h($lesson['MonHoc']) ?></h2><p class="muted">Ngày: <?= h($lesson['NgayHoc']) ?> · Buổi: <?= h($selected) ?> · <?= h($lesson['NoiDung']) ?></p>
<?php if($user['VaiTro']==='QUAN_TRI_VIEN'): ?><div class="role-note">Quản trị viên chỉ xem; giảng viên phụ trách mới được lưu điểm danh.</div><?php endif; ?>
<form method="post"><?= csrf_field() ?><input type="hidden" name="buoi" value="<?= h($selected) ?>">
<div class="scroll"><table class="data-table"><thead><tr><th>Mã SV</th><th>Họ tên</th><th>Trạng thái</th><th>Ghi chú</th></tr></thead><tbody>
<?php foreach($students as $sv): $code=$sv['MaSV']; $cur=$_POST['status'][$code]??$records[$code]['TrangThai']??''; $note=$_POST['note'][$code]??$records[$code]['GhiChu']??''; ?>
<tr class="attendance-row"><td><?= h($code) ?></td><td><?= h($sv['HoTen']) ?></td><td><select name="status[<?= h($code) ?>]" required <?= $user['VaiTro']==='QUAN_TRI_VIEN'?'disabled':'' ?>><option value="" <?= $cur===''?'selected':'' ?>>-- Chọn trạng thái --</option><?php foreach($statuses as $status): ?><option value="<?= h($status) ?>" <?= $status===$cur?'selected':'' ?>><?= h($status) ?></option><?php endforeach; ?></select></td>
<td><input style="max-width:260px" name="note[<?= h($code) ?>]" maxlength="255" value="<?= h((string)$note) ?>" placeholder="Ghi chú (nếu có)" <?= $user['VaiTro']==='QUAN_TRI_VIEN'?'disabled':'' ?>></td></tr>
<?php endforeach; ?>
<?php if(!$students): ?><tr><td colspan="4" class="empty">Chưa có sinh viên trong lớp.</td></tr><?php endif; ?>
</tbody></table></div>
<?php if($user['VaiTro']==='GIANG_VIEN' && $students): ?><p><button class="btn" type="submit">Lưu kết quả điểm danh</button></p><?php endif; ?>
</form></div>
<?php endif; ?>
<?php require __DIR__.'/includes/footer.php'; ?>
