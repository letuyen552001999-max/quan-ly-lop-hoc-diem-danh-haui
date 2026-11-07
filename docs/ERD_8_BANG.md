# Quan hệ CSDL – sơ đồ ERD 8 bảng của phiên bản minh họa

Các quan hệ một–nhiều: `LOP` → `SINHVIEN`, `LOP` → `LICHHOC`, `GIANGVIEN` → `LICHHOC`, `LICHHOC` → `BUOIHOC`, `BUOIHOC` → `DIEMDANH`, `SINHVIEN` → `DIEMDANH`, `SINHVIEN` → `THONGBAO`, `BUOIHOC` → `THONGBAO`. Bảng `TAIKHOAN` có liên kết tùy chọn một–một đến SINHVIEN hoặc GIANGVIEN.

```mermaid
erDiagram
    LOP ||--o{ SINHVIEN : "gồm"
    LOP ||--o{ LICHHOC : "có"
    GIANGVIEN ||--o{ LICHHOC : "phụ trách"
    LICHHOC ||--o{ BUOIHOC : "gồm"
    BUOIHOC ||--o{ DIEMDANH : "ghi nhận"
    SINHVIEN ||--o{ DIEMDANH : "được điểm danh"
    SINHVIEN ||--o{ THONGBAO : "nhận"
    BUOIHOC |o--o{ THONGBAO : "liên quan"
    SINHVIEN |o--o| TAIKHOAN : "có thể có"
    GIANGVIEN |o--o| TAIKHOAN : "có thể có"
```

**Lưu ý:** MySQL bảo đảm PK/FK/UNIQUE. Quy tắc "sinh viên phải thuộc lớp của buổi học" còn được kiểm tra tại `attendance.php`, vì hai khóa ngoại riêng không bảo đảm được ràng buộc này.
