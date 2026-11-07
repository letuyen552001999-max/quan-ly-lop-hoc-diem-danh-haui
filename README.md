# HỆ THỐNG QUẢN LÝ LỚP HỌC VÀ ĐIỂM DANH – NHÓM 15

**Học phần:** Thực tập cơ sở ngành, HaUI  
**GV hướng dẫn:** ThS. Vũ Anh Tú  
**Thành viên:** Lê Đức Tuyền – Vũ Quý Thuận – Hoàng Quốc Việt

## 1. Mục đích

Đây là phần mềm **thử nghiệm phục vụ học tập** cho đề tài quản lý lớp và điểm danh. Không kết nối dữ liệu, tài khoản hoặc hệ thống chính thức của Đại học Công nghiệp Hà Nội. Bản mã nguồn và báo cáo Word được xây dựng dựa trên thiết kế của nhóm, gồm 6 bảng nghiệp vụ gốc và 2 bảng hỗ trợ.

**Công nghệ:** PHP 8+, PDO MySQL, MySQL/MariaDB, HTML/CSS, XAMPP; không cần cài Composer. Giao diện tiếng Việt.

## 2. Cài đặt từ đầu trên Windows

1. Giải nén ZIP, sao chép nguyên thư mục `QL_LopHoc_DiemDanh_Nhom15_HoanThien` vào `C:\xampp\htdocs\`.
2. Mở XAMPP Control Panel, **Start** Apache và MySQL.
3. Mở `http://localhost/phpmyadmin/` và tạo cơ sở dữ liệu trống `ql_lophoc_diemdanh` theo mã `utf8mb4_unicode_ci` (nếu có); vào **Import** chọn `sql/schema.sql` rồi **Go**. Tệp SQL cũng có `CREATE DATABASE` và `USE`.
4. Xác nhận đã có **8 bảng**: `LOP`, `SINHVIEN`, `GIANGVIEN`, `LICHHOC`, `BUOIHOC`, `DIEMDANH`, `TAIKHOAN`, `THONGBAO`.
5. Trên cùng máy XAMPP, mở `http://localhost/QL_LopHoc_DiemDanh_Nhom15_HoanThien/setup.php` và **tự đặt** 3 mật khẩu (mỗi mật khẩu ≥10 ký tự, khác nhau).
6. Truy cập `http://localhost/QL_LopHoc_DiemDanh_Nhom15_HoanThien/` và đăng nhập.

**Tài khoản mẫu:** `admin` (Quản trị viên), `gv01` (Giảng viên), `sv001` (Sinh viên). Không có mật khẩu chung được gắn cứng trong chương trình.

Khi Apache dùng cổng 8080, thêm `:8080` vào URL. Khi MySQL có mật khẩu, sửa cấu hình trong `config/database.php` hoặc khai báo biến môi trường `APP_DB_HOST`, `APP_DB_PORT`, `APP_DB_NAME`, `APP_DB_USER`, `APP_DB_PASS` (không đẩy mật khẩu thật lên Git).

**Nếu từng cài bản trước**: phải sao lưu dữ liệu; không tự xóa CSDL đang dùng. Chỉ nhập lại file `schema.sql` trong cơ sở dữ liệu thử nghiệm riêng khi đã hiểu dữ liệu mẫu. Trang `setup.php` chỉ mở trên localhost khi **chưa có tài khoản**.

## 3. Mức độ hoàn thành theo mã nguồn

| Chức năng | Nội dung | Người sử dụng |
|---|---|---|
| Đăng nhập và đổi mật khẩu | Xác thực mật khẩu băm, giới hạn số lần thử theo phiên; đổi mật khẩu sau đăng nhập | 3 vai trò |
| Phân quyền | Giới hạn truy cập ở máy chủ theo 3 vai trò cố định | 3 vai trò |
| Quản lý lớp | Danh sách, thêm, sửa, xóa | Quản trị viên |
| Quản lý sinh viên | Tìm kiếm, thêm, sửa, xóa; khóa ngoại, kiểm tra trùng mã | Quản trị viên; giảng viên xem lớp phụ trách |
| Quản lý giảng viên | Tìm kiếm, thêm, sửa, xóa | Quản trị viên |
| Quản lý lịch học | Xem, thêm, sửa, xóa | Quản trị viên; GV/SV xem phần liên quan |
| Quản lý buổi học | Tạo, sửa, xóa; cấm xóa buổi đã có điểm danh/thông báo | Quản trị viên, GV phụ trách |
| Điểm danh | Giảng viên ghi/sửa trạng thái, không tự mặc định có mặt | GV phụ trách |
| Lịch sử, tỷ lệ tham gia | Tra cứu theo lớp/khoảng ngày có kiểm tra quyền; tỷ lệ = (Có mặt + Đi muộn)/tổng bản ghi phù hợp | Cả 3 vai trò |
| Thông báo | Phát thông báo khi đổi trạng thái; sinh viên xem, đánh dấu đã đọc | Sinh viên |

**Giới hạn:** phân quyền hiện cố định theo vai trò, không có màn hình gán quyền tùy ý; chưa có QR, gửi email, nhật ký lịch sử sửa điểm danh, xuất Excel, phân trang lịch sử nâng cao. Không triển khai Internet thực tế.

## 4. Quy trình demo gợi ý trước thầy

1. Mở VS Code, giới thiệu cấu trúc thư mục `assets/`, `config/`, `includes/`, `sql/`, `docs/`.
2. Mở phpMyAdmin, xem sơ đồ khóa chính và khóa ngoại trong 8 bảng; xem dữ liệu mẫu `SV001`, `GV001`, `B001`.
3. Đăng nhập `admin`, thực hiện **Thêm SV004 → Xem danh sách → Sửa họ tên → Xóa SV004**, sau đó thử nhập trùng `SV001` để xem lỗi.
4. Thêm một lớp mới, giảng viên và lịch học (nếu cần), tạo buổi `B002` cho `GV001`.
5. Đăng nhập `gv01`, mở buổi `B001`, tự chọn trạng thái đầy đủ rồi lưu. Đăng nhập `sv001` để xem lịch sử và thông báo.
6. Thử xóa `SV001` đã có điểm danh và truy cập chức năng quản trị khi đăng nhập vai trò giảng viên; đối chiếu thông báo ngăn thao tác.

**Chỉ sau khi tự chạy mới ghi “Đạt” vào bảng kiểm thử.** Chụp màn hình trong XAMPP/phpMyAdmin cho Chương 4 báo cáo.

## 5. Kiểm tra kỹ thuật đã thực hiện

- Kiểm tra cú pháp với `php -l` cho tất cả các tệp `.php`. Trên Windows có thể chạy `tests/kiem_tra_php_windows.ps1` để tự kiểm tra đồng loạt.
- Chạy `php tests/test_helpers.php` để kiểm tra **29 trường hợp hàm** không cần CSDL (escaping, vai trò, CSRF, dữ liệu, thời gian, tỷ lệ).
- **Chưa xác nhận** chạy xuyên suốt các thao tác trên MySQL/XAMPP tại máy nhóm. Cần làm thử theo `docs/BANG_KIEM_THU.md`.

Xem thêm `docs/KET_QUA_KIEM_TRA_KY_THUAT.txt` để phân biệt kết quả kiểm tra đã thực hiện với các ca MySQL chưa chạy.

## 6. Cấu trúc chính

```
QL_LopHoc_DiemDanh_Nhom15_HoanThien/
├─ assets/style.css                    CSS chung
├─ config/database.php                Kết nối CSDL (PDO)
├─ includes/bootstrap.php             Đăng nhập, CSRF, quyền
├─ includes/validation.php            Kiểm tra dữ liệu
├─ sql/schema.sql                     8 bảng, khóa và dữ liệu mẫu
├─ setup.php, login.php, logout.php, account.php   Tài khoản và mật khẩu
├─ index.php                          Trang chủ
├─ classes.php, class_form.php, class_delete.php
├─ students.php, student_form.php, student_delete.php
├─ teachers.php, teacher_form.php, teacher_delete.php
├─ schedule.php, schedule_form.php, schedule_delete.php
├─ lessons.php, lesson_form.php, lesson_delete.php
├─ attendance.php, history.php, notifications.php
├─ tests/test_helpers.php             Kiểm tra không cần CSDL
├─ tests/test_database.php            Kiểm tra MySQL có hoàn tác dữ liệu
└─ docs/                              ERD, đối chiếu, demo, kiểm thử
```

## 7. Lưu ý làm việc nhóm

Mỗi thành viên giữ một nhánh Git, tránh sửa trực tiếp cùng tệp. Không lưu mật khẩu thật hay dữ liệu sinh viên thật lên Git. Báo cáo Word phân biệt rõ **bản thiết kế**, **mã đã viết**, **kiểm tra tĩnh đã chạy** và **kết quả chạy XAMPP chưa kiểm chứng**.

## 8. Lần rà soát bổ sung

- `history.php`: bổ sung lọc theo lớp, ngày bắt đầu/kết thúc; luôn giới hạn quyền truy cập từ phiên đăng nhập; số liệu tổng hợp được tính trên toàn bộ kết quả lọc, không chỉ 500 dòng hiển thị.
- `attendance.php`: đưa thao tác đọc trạng thái cũ vào cùng giao dịch lưu để hạn chế thông báo trùng/sai khi thao tác gần nhau.
- Thêm các header bảo vệ cơ bản, kiểm tra dữ liệu CSRF có đúng kiểu chuỗi, và `.htaccess` chặn truy cập trực tiếp vào các thư mục code/cấu hình khi chạy Apache.
- Thêm các trường hợp kiểm tra hàm, 5 ca kiểm thử nghiệp vụ cho XAMPP và tài liệu câu hỏi bảo vệ trong `docs/CAU_HOI_BAO_VE.md`.

**Giới hạn xác minh:** môi trường soạn mã không có MySQL/MariaDB và trình điều khiển PDO MySQL, nên các ca kiểm thử CSDL vẫn cần chạy trên máy nhóm.

## 9. Phần bổ sung ở bản cuối

- Trang `account.php`: người dùng đã đăng nhập tự đổi mật khẩu sau khi nhập mật khẩu cũ; xác thực và cập nhật tại máy chủ, yêu cầu tối thiểu 10 ký tự (không quá 72 byte).
- Trang `schedule_form.php`: khi lịch đã có điểm danh thì không cho đổi **lớp hoặc giảng viên** nhằm tránh sai phạm vi lịch sử và quyền điểm danh.
- `tests/test_database.php`: bài thử tích hợp với MySQL, dùng giao dịch và luôn `ROLLBACK`, không giữ lại dữ liệu kiểm thử; trên máy nhóm cần chạy lệnh `php tests/test_database.php` sau khi đã khởi tạo CSDL.
- **Lưu ý:** Không thể khẳng định các ca MySQL ở trên đã đạt cho tới khi nhóm chạy thật trong XAMPP. Không đưa mật khẩu hoặc dữ liệu sinh viên thật vào ảnh minh chứng.

**Chú ý khi chạy CLI:** nếu hệ thống báo thiếu `pdo_mysql`, phải dùng `C:\xampp\php\php.exe` trong PowerShell, vì PHP được cài ngoài XAMPP có thể không có trình điều khiển MySQL.
