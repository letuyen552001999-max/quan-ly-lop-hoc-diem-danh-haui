<?php
/**
 * Kiểm thử tích hợp CSDL cho máy đã cài MySQL/XAMPP.
 * Chạy: php tests/test_database.php
 * Tất cả dữ liệu thử được tạo trong giao dịch rồi ROLLBACK.
 */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit('Chỉ chạy bằng dòng lệnh.'); }
if (!extension_loaded('pdo_mysql')) {
    fwrite(STDERR, "SKIP/CHƯA CHẠY: Thiếu phần mở rộng pdo_mysql; hãy chạy bài thử bằng PHP của XAMPP khi đã bật MySQL.\n");
    exit(2);
}
require_once __DIR__ . '/../config/database.php';
function expect_db(bool $condition, string $label): void {
    if (!$condition) throw new RuntimeException('FAIL: ' . $label);
    echo 'PASS: ' . $label . PHP_EOL;
}
$pdo = db();
$tag = 'T' . strtoupper(bin2hex(random_bytes(3)));
$passed = 0;
try {
    $pdo->beginTransaction();
    $count = (int)$pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name IN ('LOP','SINHVIEN','GIANGVIEN','LICHHOC','BUOIHOC','DIEMDANH','TAIKHOAN','THONGBAO')")->fetchColumn();
    expect_db($count === 8, 'Có đủ 8 bảng'); $passed++;
    $pdo->prepare('INSERT INTO LOP(MaLop,TenLop,Khoa) VALUES(?,?,?)')->execute([$tag,'Lớp kiểm thử','K19']);
    $pdo->prepare('INSERT INTO GIANGVIEN(MaGV,HoTen,BoMon) VALUES(?,?,?)')->execute([$tag,'Giảng viên thử','KTPM']);
    $pdo->prepare('INSERT INTO SINHVIEN(MaSV,HoTen,MaLop) VALUES(?,?,?)')->execute([$tag,'Sinh viên thử',$tag]);
    $st=$pdo->prepare('SELECT HoTen FROM SINHVIEN WHERE MaSV=?');$st->execute([$tag]);
    expect_db($st->fetchColumn()==='Sinh viên thử','Thêm và đọc sinh viên'); $passed++;
    $pdo->prepare('UPDATE SINHVIEN SET HoTen=? WHERE MaSV=?')->execute(['Sinh viên đã sửa',$tag]);
    $st->execute([$tag]);expect_db($st->fetchColumn()==='Sinh viên đã sửa','Sửa và đọc lại sinh viên');$passed++;
    try {
        $pdo->prepare('INSERT INTO SINHVIEN(MaSV,HoTen,MaLop) VALUES(?,?,?)')->execute([$tag,'Trùng mã',$tag]);
        throw new RuntimeException('Khóa chính đã cho phép mã trùng');
    } catch (PDOException $e) {
        expect_db($e->getCode()==='23000','Chặn mã sinh viên trùng');$passed++;
    }
    $pdo->prepare('INSERT INTO LICHHOC (MaLop,MaGV,MonHoc,Thu,GioBatDau,GioKetThuc,Phong) VALUES (?,?,?,?,?,?,?)')->execute([$tag,$tag,'Môn kiểm thử',2,'08:00:00','10:00:00','P01']);
    $id=(int)$pdo->lastInsertId();
    $pdo->prepare('INSERT INTO BUOIHOC (MaBuoi,MaLich,NgayHoc,NoiDung) VALUES (?,?,?,?)')->execute([$tag,$id,'2026-10-26','Buổi thử']);
    $pdo->prepare('INSERT INTO DIEMDANH (MaBuoi,MaSV,TrangThai,ThoiGian,GhiChu) VALUES (?,?,?,?,?)')->execute([$tag,$tag,'Có mặt','08:00:00','']);
    $st=$pdo->prepare('SELECT TrangThai FROM DIEMDANH WHERE MaBuoi=? AND MaSV=?');$st->execute([$tag,$tag]);
    expect_db($st->fetchColumn()==='Có mặt','Lưu điểm danh theo buổi');$passed++;
    $pdo->prepare('UPDATE DIEMDANH SET TrangThai=? WHERE MaBuoi=? AND MaSV=?')->execute(['Đi muộn',$tag,$tag]);
    $st->execute([$tag,$tag]); expect_db($st->fetchColumn()==='Đi muộn','Điều chỉnh trạng thái điểm danh');$passed++;
    try {
        $pdo->prepare('DELETE FROM SINHVIEN WHERE MaSV=?')->execute([$tag]);
        throw new RuntimeException('Đã xóa sinh viên dù có điểm danh');
    } catch (PDOException $e) {
        expect_db($e->getCode()==='23000','Khóa ngoại ngăn xóa SV có điểm danh');$passed++;
    }
    $pdo->prepare('INSERT INTO THONGBAO (MaSV,MaBuoi,NoiDung) VALUES (?,?,?)')->execute([$tag,$tag,'Đã thay đổi trạng thái']);
    $st=$pdo->prepare('SELECT COUNT(*) FROM THONGBAO WHERE MaSV=? AND MaBuoi=?');$st->execute([$tag,$tag]);
    expect_db((int)$st->fetchColumn()===1,'Lưu thông báo cập nhật');$passed++;
    $pdo->prepare('DELETE FROM THONGBAO WHERE MaSV=?')->execute([$tag]);
    $pdo->prepare('DELETE FROM DIEMDANH WHERE MaBuoi=?')->execute([$tag]);
    $pdo->prepare('DELETE FROM BUOIHOC WHERE MaBuoi=?')->execute([$tag]);
    $pdo->prepare('DELETE FROM LICHHOC WHERE MaLich=?')->execute([$id]);
    $pdo->prepare('DELETE FROM SINHVIEN WHERE MaSV=?')->execute([$tag]);
    $st->execute([$tag,$tag]);
    $s=$pdo->prepare('SELECT COUNT(*) FROM SINHVIEN WHERE MaSV=?');$s->execute([$tag]);
    expect_db((int)$s->fetchColumn()===0,'Xóa sinh viên khi không còn ràng buộc');$passed++;
    $pdo->rollBack();
    echo "KẾT QUẢ: {$passed}/{$passed} ca tích hợp đã chạy đạt; ROLLBACK dữ liệu thử." . PHP_EOL;
} catch (Throwable $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    fwrite(STDERR, $e->getMessage().PHP_EOL);
    exit(1);
}
