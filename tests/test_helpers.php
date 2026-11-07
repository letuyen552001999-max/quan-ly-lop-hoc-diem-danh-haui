<?php
// Chạy: php tests/test_helpers.php (không yêu cầu MySQL).
require_once __DIR__ . '/../includes/bootstrap.php';
function check(bool $value, string $label): void
{
    if (!$value) {
        fwrite(STDERR, "FAIL: {$label}\n");
        exit(1);
    }
    echo "PASS: {$label}\n";
}
check(h('<script>') === '&lt;script&gt;', 'Escape HTML');
check(role_name('GIANG_VIEN') === 'Giảng viên', 'Tên vai trò');
check(strlen(csrf_token()) === 64, 'CSRF token được tạo');
check(in_array('Có phép', ['Có mặt','Vắng','Đi muộn','Có phép'],true), 'Danh sách trạng thái điểm danh');
check(is_file(__DIR__ . '/../sql/schema.sql'), 'Có tệp SQL');
check(check_code('SV_01','Mã')===[], 'Mã hợp lệ');
check(count(check_code('mã lỗi','Mã'))===1, 'Mã không hợp lệ bị từ chối');
check(check_text('Nguyễn Văn A','Họ tên')===[], 'Tên tiếng Việt hợp lệ');
check(!check_weekday(9) && check_weekday(8), 'Kiểm tra thứ trong tuần');
check(check_hours('08:00','10:30') && !check_hours('11:00','10:00'), 'Kiểm tra thời gian');
check(check_date('2026-10-26') && !check_date('2026-02-30'), 'Kiểm tra ngày tháng');
check(attendance_ratio(['Có mặt'=>2,'Vắng'=>1,'Đi muộn'=>1,'Có phép'=>1])===60.0, 'Tính tỷ lệ tham gia');
check(check_code('SV001','Mã')===[], 'Mã sinh viên thông dụng');
check(count(check_code('SV 001','Mã'))===1, 'Mã có dấu cách bị từ chối');
check(check_text(' ','Tên')!==[], 'Tên chỉ có dấu cách bị từ chối');
check(check_text(str_repeat('a',101),'Tên')!==[], 'Tên dài quá giới hạn bị từ chối');
check(check_weekday(2) && !check_weekday(1), 'Ngày thứ Hai và ngoài phạm vi');
check(!check_hours('12:30','12:30'), 'Bắt đầu và kết thúc trùng nhau bị từ chối');
check(!check_hours('24:00','25:00'), 'Giờ ngoài phạm vi bị từ chối');
check(check_date('2028-02-29') && !check_date('2027-02-29'), 'Năm nhuận và ngày không hợp lệ');
check(attendance_ratio(['Có mặt'=>0,'Vắng'=>0])===0.0, 'Không chia cho 0 khi chưa có bản ghi');
check(attendance_ratio(['Có mặt'=>1,'Vắng'=>0,'Đi muộn'=>0,'Có phép'=>0])===100.0, 'Tỷ lệ tham gia 100%');
check(role_name('SINH_VIEN')==='Sinh viên', 'Tên vai trò sinh viên');
check(!has_role('QUAN_TRI_VIEN'), 'Không có quyền quản trị khi chưa đăng nhập');
check(password_errors('abc')!==[], 'Từ chối mật khẩu ngắn');
check(password_errors('MatKhauManh123')===[], 'Chấp nhận mật khẩu đủ độ dài');
check(password_errors(str_repeat('a',73))!==[], 'Từ chối mật khẩu quá 72 byte');
check(password_errors('          ')!==[], 'Từ chối mật khẩu chỉ gồm dấu cách');
check(password_errors('ChuoiAnToan')===[], 'Chuỗi Unicode/ASCII hợp lệ với chính sách');
echo "Đã chạy xong 29 phép kiểm tra hàm không cần CSDL.\n";
