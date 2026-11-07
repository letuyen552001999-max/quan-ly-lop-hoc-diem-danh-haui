# KIỂM THỬ VẬN HÀNH TRÊN XAMPP – NHÓM 15

Bảng này là kế hoạch chạy thử, không phải kết quả đã được xác nhận.

| Mã | Thao tác thực hiện | Kết quả mong đợi | Thực tế |
|---|---|---|---|
| TC01 | Đăng nhập sai mật khẩu | Bị từ chối | Chưa chạy XAMPP |
| TC02 | `admin` đăng nhập đúng | Trang chủ có quyền quản trị | Chưa chạy XAMPP |
| TC03 | Admin thêm `SV004` vào `L01` | Xuất hiện trong bảng SINHVIEN | Chưa chạy XAMPP |
| TC04 | Thêm lại `SV001` | Báo mã đã tồn tại | Chưa chạy XAMPP |
| TC05 | Thêm sinh viên thiếu họ tên | Báo lỗi và không thêm | Chưa chạy XAMPP |
| TC06 | Sửa tên `SV004` | Tên mới được lưu | Chưa chạy XAMPP |
| TC07 | Xóa `SV004` chưa có dữ liệu liên quan | Xóa thành công | Chưa chạy XAMPP |
| TC08 | Xóa `SV001` đã điểm danh | Chặn xóa để giữ lịch sử | Chưa chạy XAMPP |
| TC09 | Admin tạo lớp `L03` | Có lớp mới trong LOP | Chưa chạy XAMPP |
| TC10 | Admin xóa `L01` đã có sinh viên | Chặn xóa | Chưa chạy XAMPP |
| TC11 | Admin thêm giảng viên `GV002` | Thêm thành công | Chưa chạy XAMPP |
| TC12 | Admin tạo lịch học | Có bản ghi mới trong LICHHOC | Chưa chạy XAMPP |
| TC13 | GV tạo buổi `B002` cho lớp phụ trách | Có bản ghi BUOIHOC | Chưa chạy XAMPP |
| TC14 | GV tạo buổi cho lớp không được giao | Bị từ chối | Chưa chạy XAMPP |
| TC15 | GV chưa chọn trạng thái điểm danh | Form không ghi Có mặt tự động | Chưa chạy XAMPP |
| TC16 | GV ghi trạng thái và cập nhật lại | DIEMDANH cập nhật, tạo thông báo | Chưa chạy XAMPP |
| TC17 | GV gửi kèm mã SV ngoài lớp | Máy chủ từ chối | Chưa chạy XAMPP |
| TC18 | SV xem lịch sử | Chỉ thấy điểm danh của bản thân | Chưa chạy XAMPP |
| TC19 | SV xem thông báo, đánh dấu đọc | Chỉ sửa thông báo của mình | Chưa chạy XAMPP |
| TC20 | GV truy cập trực tiếp `class_form.php` | Trả 403 | Chưa chạy XAMPP |
| TC21 | Gửi POST xóa không kèm CSRF | Trả 400 | Chưa chạy XAMPP |
| TC22 | Đăng xuất và mở trang nội bộ | Chuyển về đăng nhập | Chưa chạy XAMPP |

**Kiểm tra không cần CSDL (đã thực hiện trong môi trường soạn mã):** cú pháp các file PHP qua `php -l`; 12 bài kiểm tra hàm qua `tests/test_helpers.php`. Không thay thế kết quả trên MySQL.

**Minh chứng nên lưu:** ảnh danh sách bảng trong phpMyAdmin, giao diện 3 vai trò, luồng thêm/sửa/xóa sinh viên, thông báo trùng mã, màn hình điểm danh, lịch sử và thông báo, lỗi phân quyền. Điền Ngày kiểm thử/Người thực hiện/Đạt–Chưa đạt sau khi thực sự chạy.

### Kiểm tra bổ sung sau lượt rà soát mã nguồn

| Mã | Thao tác | Kết quả mong đợi | Thực tế |
|---|---|---|---|
| TC23 | Lọc lịch sử theo khoảng ngày hợp lệ | Danh sách và thống kê chỉ tính các bản ghi trong khoảng ngày | Chưa chạy XAMPP |
| TC24 | Ngày kết thúc nhỏ hơn ngày bắt đầu | Báo lỗi, không chạy truy vấn kết quả | Chưa chạy XAMPP |
| TC25 | GV thử chọn lớp không phụ trách trong đường dẫn `history.php?lop=...` | Báo lớp không thuộc phạm vi tra cứu | Chưa chạy XAMPP |
| TC26 | Có trên 500 lượt điểm danh phù hợp | Danh sách tối đa 500 nhưng tổng và tỷ lệ tính trên toàn bộ bản ghi phù hợp | Chưa chạy XAMPP |
| TC27 | Nhấn lưu lại cùng trạng thái/ghi chú | Không tạo thông báo trùng do không có thay đổi thực tế | Chưa chạy XAMPP |

**Kiểm tra không dùng CSDL:** chạy `tests/kiem_tra_php_windows.ps1` (hoặc `php tests/test_helpers.php`). Các kiểm thử HTTP/SQL cần chạy thêm sau khi cấu hình XAMPP.
