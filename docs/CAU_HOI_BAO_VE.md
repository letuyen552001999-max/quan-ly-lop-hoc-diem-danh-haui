# CÂU HỎI THỰC HÀNH VÀ BẢO VỆ – NHÓM 15

*Các câu trả lời dưới đây dùng để cả nhóm hiểu sản phẩm; khi bảo vệ hãy chỉ vào tệp code liên quan và nói bằng cách hiểu của mình.*

1. **Vì sao chọn PHP + MySQL?** Dự án chạy trên XAMPP ở máy cá nhân, dễ chia tệp theo chức năng và thao tác CSDL quan hệ. Đây là lựa chọn cho bản demo, không phải nền tảng chính thức của HaUI.
2. **Hệ thống có bao nhiêu bảng?** Tám: 6 nghiệp vụ `LOP`, `SINHVIEN`, `GIANGVIEN`, `LICHHOC`, `BUOIHOC`, `DIEMDANH`, thêm `TAIKHOAN` và `THONGBAO` để hiện thực đăng nhập/thông báo.
3. **Vì sao DIEMDANH dùng khóa ghép?** Một sinh viên chỉ có một kết quả cho một buổi học, nên khóa `(MaBuoi, MaSV)` hạn chế bản ghi trùng.
4. **Làm sao biết sinh viên được điểm danh đúng lớp?** PHP tải danh sách sinh viên từ lớp liên kết với buổi học, sau đó đối chiếu chính xác mã gửi lên trước khi lưu; đây là kiểm tra nghiệp vụ ở ứng dụng.
5. **Phân quyền ở đâu?** `includes/bootstrap.php` có `require_login()` và `require_role()`; từng trang quan trọng gọi chúng trước khi truy vấn/ghi. Giao diện ẩn nút là hỗ trợ người dùng, không thay thế kiểm tra máy chủ.
6. **Vì sao không lưu mật khẩu rõ?** `setup.php` dùng `password_hash()`, `login.php` dùng `password_verify()` để không lưu chuỗi mật khẩu trong bảng.
7. **CSRF là gì?** Là hành vi ép trình duyệt người dùng đã đăng nhập gửi biểu mẫu ngoài ý muốn; các biểu mẫu ghi/xóa kiểm tra mã CSRF do phiên sinh ra.
8. **PDO chuẩn bị truy vấn có tác dụng gì?** Tách dữ liệu nhập khỏi cấu trúc SQL, giảm nguy cơ SQL Injection và làm code dễ đọc hơn.
9. **Tỷ lệ chuyên cần tính như thế nào?** Ở bản demo: `(Có mặt + Đi muộn) / tổng bản ghi đã điểm danh * 100`. Có phép nằm trong tổng; đây không phải quy định chính thức của trường.
10. **Vì sao không cho xóa sinh viên đã điểm danh?** Khóa ngoại `ON DELETE RESTRICT` giữ dữ liệu tham chiếu; chức năng xóa bắt lỗi và báo người dùng.
11. **Một tình huống kiểm thử quan trọng?** Cho giảng viên truy cập `class_form.php` để kiểm tra HTTP 403; thêm `SV001` để kiểm tra mã trùng; gửi mã sinh viên ngoài lớp qua điểm danh để kiểm tra từ chối.
12. **Điểm hạn chế còn lại?** Chưa xác nhận chạy xuyên suốt trên XAMPP của nhóm; chưa có QR, phân quyền tự cấu hình, ghi nhật ký sửa điểm danh và báo cáo Excel. Không nên nói đã hoàn thành các phần này.

**Kịch bản trình bày ngắn:** giới thiệu bài toán → mở ERD → chọn một luồng CRUD → thử lỗi trùng mã → đăng nhập giảng viên để điểm danh → đăng nhập sinh viên để tra cứu → trình bày kết quả kiểm thử thực tế sau khi chạy.
