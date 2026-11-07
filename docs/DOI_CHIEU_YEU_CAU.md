# ĐỐI CHIẾU YÊU CẦU PHÂN TÍCH VỚI MÃ NGUỒN

| Mã | Yêu cầu | Mã nguồn liên quan | Phạm vi hiện có / Giới hạn |
|---|---|---|---|
| FR01 | Đăng nhập | `login.php`, `setup.php`, `includes/bootstrap.php` | Băm và kiểm tra mật khẩu |
| FR02 | Phân quyền | `includes/bootstrap.php`, các trang `*_form.php`, `*_delete.php` | 3 vai trò cố định; chưa có màn hình gán quyền động |
| FR03 | Quản lý lớp | `classes.php`, `class_form.php`, `class_delete.php` | CRUD lớp; chặn xóa nếu bị tham chiếu |
| FR04 | Quản lý sinh viên | `students.php`, `student_form.php`, `student_delete.php` | CRUD, tìm kiếm, lọc lớp, kiểm tra dữ liệu |
| FR05 | Quản lý giảng viên | `teachers.php`, `teacher_form.php`, `teacher_delete.php` | CRUD và tìm kiếm |
| FR06 | Quản lý lịch học | `schedule.php`, `schedule_form.php`, `schedule_delete.php`, `lessons.php`, `lesson_form.php` | CRUD lịch, quản lý buổi học; có ràng buộc xóa |
| FR07 | Điểm danh | `attendance.php` | GV phụ trách ghi trạng thái cho SV trong lớp |
| FR08 | Điều chỉnh điểm danh | `attendance.php` | GV sửa trạng thái đã ghi; chưa có lịch sử điều chỉnh |
| FR09 | Tra cứu | `history.php` | Lịch sử + tỷ lệ tham gia; chưa có lọc theo ngày |
| FR10 | Thông báo | `attendance.php`, `notifications.php` | Thông báo trong web khi trạng thái thay đổi |

**Ràng buộc:** FK ngăn xóa mất lịch sử. Quyền được xác minh trên máy chủ, không chỉ ẩn nút. Có kiểm tra CSRF ở những form ghi/xóa. Các câu lệnh có dữ liệu người dùng dùng PDO prepared statements.

**Kiểm chứng thực tế:** Chỉ đánh dấu kết quả XAMPP sau khi chạy bằng tài khoản mẫu và đối chiếu các bảng trong MySQL.
