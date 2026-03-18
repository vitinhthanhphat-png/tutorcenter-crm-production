# 🎓 TutorCenter CRM — Demo Login Guide

> **URL:** http://127.0.0.1:8000  
> **Stack:** Laravel 13 + Livewire + Tailwind CSS  
> **Demo tenant:** Trung tâm Gia sư Ánh Dương

---

## 👤 Tài khoản đăng nhập

| Role | Tên | Email | Mật khẩu | Quyền hạn |
|------|-----|-------|----------|-----------|
| **Super Admin** | Super Admin | `admin@tutorcenter.vn` | `password` | Toàn quyền hệ thống |
| **Quản lý (Manager)** | Trần Văn Quản | `manager@anhduong.vn` | `Demo@2026` | Tất cả tính năng của tenant |
| **Giáo viên 1** | Nguyễn Văn Minh | `teacher1@anhduong.vn` | `Demo@2026` | Lớp mình dạy + điểm danh |
| **Giáo viên 2** | Phạm Thị Hoa | `teacher2@anhduong.vn` | `Demo@2026` | Lớp mình dạy + điểm danh |
| **Kế toán** | Lê Thị Kế Toán | `accountant@anhduong.vn` | `Demo@2026` | Tài chính + học sinh |

---

## 🗂️ Dữ liệu Demo

### Chi nhánh (2)
| Chi nhánh | Địa chỉ |
|-----------|---------|
| Cơ sở Quận 1 | 123 Nguyễn Huệ, Q.1, TP.HCM |
| Cơ sở Quận 7 | 45 Nguyễn Thị Thập, Q.7, TP.HCM |

### Khóa học (3)
| Khóa học | Môn | Học phí | Số buổi |
|----------|-----|---------|---------|
| IELTS 6.0 General | Anh văn | 4,500,000đ | 60 buổi |
| Toán Nâng Cao | Toán | 3,200,000đ | 45 buổi |
| Lập trình Python | CNTT | 2,800,000đ | 30 buổi |

### Lớp học (4)
| Lớp | Giáo viên | Chi nhánh | Lịch | Trạng thái |
|-----|-----------|-----------|------|-----------|
| IELTS 6.0 - K12A | Nguyễn Văn Minh | Quận 1 | T3,T5,T7 · 18:00-20:00 | 🟢 Đang mở |
| Toán NC - K11B | Nguyễn Văn Minh | Quận 1 | T2,T4,T6 · 17:00-19:00 | 🟢 Đang mở |
| Python - Q7A | Phạm Thị Hoa | Quận 7 | T7,CN · 09:00-11:30 | 🟢 Đang mở |
| IELTS 5.5 - K10C | Phạm Thị Hoa | Quận 7 | T2,T4 · 19:00-21:00 | 🟡 Chuẩn bị |

### Học sinh (10)
| Trạng thái | Số lượng | Mô tả |
|------------|----------|-------|
| `studying` | 6 | Đã ghi danh, đang học |
| `lead` | 3 | Tiềm năng, chưa đăng ký |
| `dropped` | 1 | Đã nghỉ |

---

## 🔐 Phân quyền theo Role

| Trang | Manager | Teacher | Accountant |
|-------|---------|---------|-----------|
| Dashboard | ✅ | ✅ | ✅ |
| Học sinh / CRM | ✅ | ❌ | ✅ |
| Lớp học (xem) | ✅ (tất cả) | ✅ (lớp mình) | ❌ |
| Lớp học (tạo/sửa) | ✅ | ❌ | ❌ |
| Điểm danh | ✅ | ✅ | ❌ |
| Tài chính · Phiếu thu | ✅ | ❌ | ✅ |
| Báo cáo Doanh thu | ✅ | ❌ | ✅ |
| Ghi danh học sinh | ✅ | ❌ | ✅ |

---

## 🚀 Kịch bản demo gợi ý

### Demo 1: Quản lý (Manager)
1. Login `manager@anhduong.vn` / `Demo@2026`
2. Dashboard → xem KPI, lịch hôm nay, leads
3. Lớp học → Xem chi tiết IELTS 6.0 - K12A
4. Ghi danh học sinh mới vào lớp
5. Báo cáo → xem biểu đồ doanh thu

### Demo 2: Giáo viên (Teacher)
1. Login `teacher1@anhduong.vn` / `Demo@2026`
2. Lớp học → chỉ thấy 2 lớp mình dạy
3. Nhấn "Điểm danh →" cho buổi hôm nay
4. Chọn ✓/P/X/L cho từng học sinh → Lưu

### Demo 3: Kế toán (Accountant)
1. Login `accountant@anhduong.vn` / `Demo@2026`
2. Sidebar: thấy Học sinh, Tài chính, Báo cáo (không thấy Lớp học)
3. Tài chính → tạo phiếu thu mới
4. Báo cáo → xem doanh thu theo tháng

---

## 🔧 Lệnh hữu ích

```bash
# Generate sessions tháng mới
php artisan sessions:generate --month=2026-04

# Fresh seed lại dữ liệu demo
php artisan migrate:fresh --seed

# Chạy scheduler thủ công (test)
php artisan schedule:run

# Xem danh sách routes
php artisan route:list
```

---

*Tài liệu này dành cho mục đích demo/testing. Mật khẩu thực tế cần thay đổi trên môi trường production.*
