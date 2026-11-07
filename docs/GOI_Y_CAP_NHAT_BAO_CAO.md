# GỢI Ý CẬP NHẬT CHƯƠNG 4 SAU KHI CHẠY DỰ ÁN

**Quan trọng:** Tài liệu này là phần mô tả mã nguồn đã được soạn, **không phải kết quả chạy thực tế**. Chỉ cập nhật câu "đã chạy" và kết quả kiểm thử khi nhóm xác minh trên máy XAMPP.

## 4.2. Công nghệ và cấu trúc dự án

Nhóm sử dụng PHP để xử lý yêu cầu từ giao diện, MySQL/MariaDB để quản lý dữ liệu và HTML/CSS để xây dựng các màn hình chức năng. Dự án được tổ chức theo các thư mục `config`, `includes`, `assets`, `sql` và các tệp chức năng ở thư mục gốc. Việc tách mã kết nối và hàm dùng chung giúp dễ sửa chữa và gộp phần việc của thành viên.

**Các tệp tiêu biểu:** `config/database.php` (PDO), `includes/bootstrap.php` (session, phân quyền và kiểm tra CSRF), `students.php` (xem/tìm kiếm), `student_form.php` (thêm/sửa), `student_delete.php` (xóa), `attendance.php` (điểm danh).

## 4.3. Xây dựng cơ sở dữ liệu

Tệp `sql/schema.sql` định nghĩa sáu bảng nghiệp vụ kế thừa thiết kế gồm LOP, SINHVIEN, GIANGVIEN, LICHHOC, BUOIHOC, DIEMDANH. Để phục vụ đăng nhập và thông báo, nhóm bổ sung bảng TAIKHOAN và THONGBAO. Bảng DIEMDANH sử dụng khóa chính ghép MaBuoi – MaSV; các khóa ngoại hỗ trợ giữ tính nhất quán dữ liệu.

**Cần cập nhật hình ERD:** nếu sử dụng bản này, ERD cuối cùng phải có 8 bảng thay vì sơ đồ 6 bảng ban đầu ở Chương 3.

## 4.4. Kết nối cơ sở dữ liệu

Tệp `config/database.php` tạo kết nối PDO đến CSDL `ql_lophoc_diemdanh` theo bộ mã utf8mb4. Truy vấn có tham số được sử dụng cho các thao tác có dữ liệu người dùng nhập vào. Khi nhóm chạy thực tế, cần chụp ảnh danh sách sinh viên lấy từ MySQL và ảnh bản ghi sau khi thêm/sửa.

## 4.5. Giao diện nền tảng

Giao diện gồm trang đăng nhập, trang chủ, thanh điều hướng, màn hình danh sách và biểu mẫu thao tác. Các menu được hiển thị theo ba vai trò Quản trị viên, Giảng viên, Sinh viên. Việc kiểm tra quyền thực hiện lại tại phía máy chủ để tránh người dùng truy cập trực tiếp đường dẫn bị hạn chế.

## 4.6. Chức năng Quản lý sinh viên

Trong giai đoạn đầu, nhóm lựa chọn Quản lý sinh viên làm chức năng triển khai thao tác thêm, xem, sửa, xóa. Quản trị viên có thể thêm sinh viên vào một lớp đã có; tìm kiếm theo mã/họ tên; sửa tên và lớp; xóa bản ghi không bị các bảng khác tham chiếu. Hệ thống kiểm tra mã sinh viên hợp lệ, không trùng, thông tin bắt buộc và quyền người dùng. Trường hợp sinh viên đã có điểm danh sẽ không được xóa nhằm giữ dữ liệu lịch sử.

## 4.7. Kiểm thử

Chỉ điền kết quả thực tế vào bảng kiểm thử sau khi làm các thao tác trong `docs/BANG_KIEM_THU.md`. Không ghi tất cả "Đạt" trước khi chạy.

## Những ảnh nên đưa vào Word sau khi chạy

1. CSDL trong phpMyAdmin có tám bảng.
2. Giao diện đăng nhập và trang chủ với tài khoản Quản trị viên.
3. Danh sách sinh viên trước và sau khi thêm `SV004`.
4. Kết quả sửa thông tin sinh viên.
5. Thông báo khi thử thêm trùng mã hoặc xóa sinh viên đã có điểm danh.
6. Giao diện điểm danh của Giảng viên và kết quả xem từ Sinh viên.

**Ghi chú:** Báo cáo gốc Chương 4 đang mang tiêu đề *Kế hoạch triển khai hệ thống*. Khi đã có minh chứng chạy thực tế, nên đổi thành *Triển khai và kiểm thử hệ thống* rồi điều chỉnh các câu "dự kiến" sang câu mô tả công việc thực sự đã thực hiện.
