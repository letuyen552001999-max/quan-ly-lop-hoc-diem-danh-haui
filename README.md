# Hệ thống quản lý lớp học và điểm danh sinh viên

**Bài tập lớn môn Thực tập cơ sở ngành – Nhóm 15, Đại học Công nghiệp Hà Nội (HaUI)**

- **Giảng viên hướng dẫn:** ThS. Vũ Anh Tú
- **Thành viên:** Lê Đức Tuyền, Vũ Quý Thuận, Hoàng Quốc Việt
- **Công nghệ:** PHP 8+, PDO MySQL, MySQL/MariaDB, HTML, CSS và XAMPP

Hệ thống hỗ trợ quản lý lớp học, lịch học, buổi học và kết quả điểm danh của sinh viên. Dự án được xây dựng để thực hành phân tích, thiết kế và phát triển ứng dụng web, **không kết nối với hệ thống chính thức của HaUI**.

## 1. Chức năng chính

| Vai trò | Chức năng |
| --- | --- |
| **Quản trị viên** | Quản lý lớp, sinh viên, giảng viên, lịch học và buổi học; tra cứu lịch sử điểm danh. |
| **Giảng viên** | Xem lớp phụ trách, quản lý buổi học được phân công, điểm danh và điều chỉnh kết quả, xem lịch sử. |
| **Sinh viên** | Xem thông tin lớp, lịch học, lịch sử điểm danh, tỷ lệ tham gia và thông báo. |
| **Cả ba vai trò** | Đăng nhập, đổi mật khẩu; hệ thống kiểm tra quyền truy cập ở phía máy chủ. |

Trạng thái điểm danh gồm **Có mặt, Vắng, Đi muộn và Có phép**. Tỷ lệ tham gia trong bản minh họa được tính bằng `(Có mặt + Đi muộn) / tổng bản ghi điểm danh × 100%`; đây không phải quy định chuyên cần chính thức của nhà trường.

## 2. Cài đặt và chạy trên Windows

### Yêu cầu

- XAMPP có **PHP 8+** và **MySQL/MariaDB**.
- Trình duyệt web (Chrome, Edge, Firefox...).
- Không cần cài Composer.

### Các bước thực hiện

**Bước 1 – Tải mã nguồn.** Trên GitHub, chọn **Code → Download ZIP**, giải nén và đổi tên thư mục thành `QL_LopHoc_DiemDanh_Nhom15_HoanThien`. Sao chép thư mục vào:

```text
C:\xampp\htdocs\QL_LopHoc_DiemDanh_Nhom15_HoanThien\
```

Bên trong thư mục phải có trực tiếp các tệp `index.php`, `login.php`, `setup.php` và thư mục `sql/` (không đặt lồng thêm một thư mục trùng tên).

**Bước 2 – Khởi động XAMPP.** Mở XAMPP Control Panel và bấm **Start** cho **Apache** và **MySQL**.

**Bước 3 – Nhập cơ sở dữ liệu.** Truy cập <http://localhost/phpmyadmin/>; tạo cơ sở dữ liệu `ql_lophoc_diemdanh` (ưu tiên bộ mã `utf8mb4`), chọn cơ sở dữ liệu vừa tạo, vào **Import**, chọn `sql/schema.sql` rồi nhấn **Go**.

Tệp SQL có sẵn lệnh `CREATE DATABASE` và `USE`, tạo **8 bảng**: `LOP`, `SINHVIEN`, `GIANGVIEN`, `LICHHOC`, `BUOIHOC`, `DIEMDANH`, `TAIKHOAN`, `THONGBAO`, kèm dữ liệu mẫu.

**Bước 4 – Tạo tài khoản dùng thử.** Trên chính máy đang chạy XAMPP, mở:

```text
http://localhost/QL_LopHoc_DiemDanh_Nhom15_HoanThien/setup.php
```

Tự đặt **ba mật khẩu khác nhau**, mỗi mật khẩu ít nhất 10 ký tự và tối đa 72 byte. Trang này chỉ dùng để khởi tạo lần đầu khi cơ sở dữ liệu chưa có tài khoản.

| Tài khoản | Vai trò |
| --- | --- |
| `admin` | Quản trị viên |
| `gv01` | Giảng viên |
| `sv001` | Sinh viên |

**Bước 5 – Mở website.** Truy cập:

```text
http://localhost/QL_LopHoc_DiemDanh_Nhom15_HoanThien/
```

Đăng nhập bằng tài khoản và mật khẩu đã thiết lập ở Bước 4.

> **Lưu ý:** Nếu Apache sử dụng cổng 8080, thay `localhost` bằng `localhost:8080` trong địa chỉ. Nếu MySQL dùng cổng hoặc mật khẩu khác, kiểm tra `config/database.php` hoặc các biến môi trường `APP_DB_HOST`, `APP_DB_PORT`, `APP_DB_NAME`, `APP_DB_USER`, `APP_DB_PASS`. **Không đưa mật khẩu thật lên GitHub.**
>
> Nếu đã từng cài phiên bản trước, hãy **sao lưu cơ sở dữ liệu trước**. Không xóa hoặc nhập lại dữ liệu trên CSDL đang sử dụng khi chưa kiểm tra ảnh hưởng.

## 3. Cơ sở dữ liệu và cấu trúc dự án

Dự án có sáu bảng nghiệp vụ chính và hai bảng hỗ trợ tài khoản, thông báo. Sơ đồ và phần giải thích được lưu tại [ERD 8 bảng](docs/ERD_8_BANG.md).

```text
QL_LopHoc_DiemDanh_Nhom15_HoanThien/
├── assets/                  CSS giao diện
├── config/database.php      Kết nối MySQL bằng PDO
├── includes/                Xác thực, phân quyền và kiểm tra dữ liệu
├── sql/schema.sql           Cấu trúc 8 bảng và dữ liệu mẫu
├── tests/                   Bài kiểm tra PHP và CSDL
├── docs/                    ERD, kịch bản demo và tài liệu kiểm thử
├── login.php, setup.php     Đăng nhập và khởi tạo tài khoản
├── index.php                Trang chủ
├── classes.php, students.php, teachers.php
├── schedule.php, lessons.php, attendance.php
├── history.php, notifications.php, account.php
└── README.md
```

## 4. Kiểm tra và chạy thử

Từ thư mục dự án, mở Git Bash, CMD hoặc PowerShell có PHP trong PATH và chạy:

```bash
php tests/test_helpers.php
```

Trong phiên bản mã nguồn này, tệp trên có **29 phép kiểm tra hàm không cần MySQL**. Để kiểm tra cú pháp PHP trên Windows, có thể chạy tệp `tests/kiem_tra_php_windows.ps1` bằng PowerShell theo hướng dẫn của nhóm.

Sau khi thiết lập CSDL, có thể chạy bài kiểm tra tích hợp:

```powershell
C:\xampp\php\php.exe tests\test_database.php
```

Chương trình kiểm tra CSDL có sử dụng giao dịch và `ROLLBACK`; tuy nhiên, nên thực hiện trên **cơ sở dữ liệu thử nghiệm**, không phải dữ liệu thật. Các tình huống kiểm thử thủ công được ghi tại [Bảng kiểm thử](docs/BANG_KIEM_THU.md).

**Phạm vi xác minh:** Các phép kiểm tra hàm và cú pháp PHP không chứng minh toàn bộ website đã chạy đúng với MySQL. Nhóm cần chạy thử trên XAMPP, ghi kết quả thực tế và chụp minh chứng trước khi báo cáo hoàn tất kiểm thử tích hợp.

## 5. Kịch bản demo ngắn

1. Đăng nhập bằng `admin`, giới thiệu danh sách lớp, sinh viên, giảng viên và lịch học.
2. Thêm một sinh viên thử nghiệm, sửa thông tin, sau đó xóa; thử mã sinh viên trùng để quan sát thông báo kiểm tra dữ liệu.
3. Đăng nhập `gv01`, chọn buổi học thuộc lớp phụ trách, cập nhật trạng thái điểm danh.
4. Đăng nhập `sv001`, xem lịch sử, tỷ lệ tham gia và thông báo tương ứng.
5. Kiểm tra giới hạn quyền bằng cách thử truy cập chức năng quản trị khi đang đăng nhập tài khoản giảng viên hoặc sinh viên.

Xem [Kịch bản demo](docs/KICH_BAN_DEMO.md) để thực hiện theo trình tự chi tiết.

## 6. Giới hạn và lưu ý khi sử dụng

- Phân quyền hiện được cố định theo ba vai trò; chưa có giao diện gán quyền tùy ý.
- Chưa triển khai điểm danh QR, xuất Excel, gửi email hoặc nhật ký lịch sử sửa điểm danh.
- Website phục vụ **học tập trên localhost**, chưa được cấu hình và kiểm thử để triển khai công khai lên Internet.
- Chỉ sử dụng dữ liệu mẫu. Không tải mật khẩu, dữ liệu sinh viên thật hoặc thông tin cá nhân lên repository Public.

**Tài liệu tham khảo trong dự án:** [ERD](docs/ERD_8_BANG.md) · [Bảng kiểm thử](docs/BANG_KIEM_THU.md) · [Kịch bản demo](docs/KICH_BAN_DEMO.md) · [Câu hỏi bảo vệ](docs/CAU_HOI_BAO_VE.md).

© Nhóm 15 – Bài tập lớn Thực tập cơ sở ngành, HaUI. Việc cấp phép tái sử dụng mã nguồn sẽ được thống nhất trong nhóm trước khi bổ sung tệp LICENSE.
