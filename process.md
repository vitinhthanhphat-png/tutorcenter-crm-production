# TutorCenter CRM — Process Tracker

> **Cập nhật lần cuối:** 19/03/2026 11:35
> **Trạng thái:** ✅ Phases 1–14 hoàn thành. QA test 6 roles + 10 bugs fixed. Portal, Multi-Tenant verified.

---

## 🎯 Agent Status

| Phase | Agent | Nội dung | Trạng thái |
|-------|-------|---------|-----------|
| 1 — Database | DB Architect | 11 migrations, models, BelongsToTenant trait, seeder | ✅ DONE |
| 2 — Backend | Backend Specialist | 6 controllers + EnrollmentController | ✅ DONE |
| 3 — Frontend | Frontend Specialist | 10 views + layouts/app.blade.php | ✅ DONE |
| 4 — Security | Security Auditor | fillable, CSRF, tenant scope, logout fix | ✅ DONE |
| 5 — Sessions | Backend Specialist | SessionGeneratorService, artisan command, RoleMiddleware | ✅ DONE |
| 6 — RBAC + Chart | Backend + Frontend | Role guards, Scheduler, revenue report, role-aware sidebar | ✅ DONE |
| 7 — Demo Data | DB Architect | DemoSeeder với 5 roles, 4 lớp, 10 học sinh, sessions | ✅ DONE |
| 8 — Super Admin | Backend + Frontend | Admin panel: tenants, users, branches, settings CRUD | ✅ DONE |
| 9 — Multi-Tenant | Backend + Frontend | staff_assignments, dispatch_requests, approval workflow | ✅ DONE |
| 10 — CRM + Finance | Backend + Frontend | CRM Leads, Cashbook, Payroll tính tự động | ✅ DONE |
| 11 — Calendar + Export | Backend + Frontend | Calendar FullCalendar API, Export CSV/JSON, TransferController | ✅ DONE |
| 12 — Portal + Analytics | Backend + Frontend | Student/Parent portal, Audit Log trait, Dashboard analytics | ✅ DONE |
| 13 — PDF + Grades + Reserve | Backend + Frontend | PDF Export (DomPDF), Bảng điểm, Bảo lưu khóa học | ✅ DONE |
| 14 — QA Test + Bug Fix | Debugger + QA | Test 5 roles, fix 7 bugs, route order, seeder leads | ✅ DONE |

---

## 🔐 Super Admin Panel (Phase 8)

URL: `http://127.0.0.1:8000/admin` → Role: `super_admin`

| Trang | URL | Tính năng |
|-------|-----|-----------|
| System Overview | `/admin` | KPI grid, doanh thu 6 tháng, user mới nhất |
| Tenants | `/admin/tenants` | List + Thêm/Sửa/Khóa/Xóa tenant |
| Tenant Detail | `/admin/tenants/{id}` | Stats, branches, users, classes của tenant |
| Chi nhánh | `/admin/branches` | List tất cả branches cross-tenant |
| Users | `/admin/users` | List + Toggle active, Reset password, Edit, Delete |
| Assignments | `/admin/assignments` | Cross-tenant staff assignments |
| Dispatch Requests | `/admin/dispatch-requests` | Approve/Reject điều phối nhân sự |
| Audit Log | `/admin/audit` | Xem log mọi thao tác hệ thống |
| Settings | `/admin/settings` | PHP/Laravel/DB/Cache environment info |

---

## 🎓 Student / Parent Portal (Phase 12)

URL: `http://127.0.0.1:8000/portal` → Role: `student`, `parent`

| Trang | URL | Tính năng |
|-------|-----|-----------|
| Trang chủ | `/portal` | Lịch học 14 ngày, điểm danh gần đây, học phí chưa TT, lớp đang học |
| Điểm danh | `/portal/attendance` | Lịch sử điểm danh, filter theo tháng, tổng hợp có mặt/vắng/muộn |
| Học phí | `/portal/invoices` | Lịch sử hóa đơn, KPI tổng đã TT + chưa TT |

---

## 📊 Dashboard Analytics (Phase 12)

| Metric | Nguồn dữ liệu |
|--------|--------------|
| Tỷ lệ điểm danh tháng này (%) | `attendances` JOIN `class_sessions` |
| Số học sinh nghỉ học tháng này | `students` WHERE `status=inactive` |
| Học phí quá hạn (top 5) | `invoices` WHERE `status=pending AND due_date < today` |
| Revenue trend 6 tháng | `invoices` GROUP BY month (Chart.js bar) |
| Student growth 6 tháng | `students` GROUP BY created_at month |

---

## 🔍 Audit Log (Phase 12)

Trait `HasAuditLog` → boot hook → `AuditLog::log()` tự động ghi mỗi khi:
- Model **created** → ghi `new_values` toàn bộ attributes
- Model **updated** → ghi `old_values` (original) + `new_values` (changes only)
- Model **deleted** → ghi `old_values` toàn bộ attributes

**Áp dụng cho:** `Student`, `Enrollment`, `Payroll`

---

## ▶️ Cách tiếp tục (nếu bị ngắt)

```bash
# 1. Start Laravel server
php artisan serve

# 2. Check current state
php artisan migrate:status
php artisan route:list

# 3. Reload demo data
php artisan migrate:fresh --seed --force

# 4. Rebuild CSS (bắt buộc sau khi thêm views mới)
npm run build

# 5. Generate sessions for a new month
php artisan sessions:generate --month=2026-04
```

**Đăng nhập Super Admin:** `admin@tutorcenter.vn` / `SuperAdmin@2026` → http://127.0.0.1:8000
**Đăng nhập Manager:** `manager@anhduong.vn` / `Demo@2026` → http://127.0.0.1:8000

> 📄 Xem full credentials tất cả roles: `demo.md` (trong thư mục gốc project)

---

## 📁 Cấu trúc File Chính

```
app/
├── Console/
│   ├── Commands/GenerateMonthlySessions.php   # artisan sessions:generate
│   └── Kernel.php                             # Scheduler (monthly, 1st at 1am)
├── Http/
│   ├── Controllers/
│   │   ├── AdminController.php                # Super Admin CRUD
│   │   ├── DashboardController.php            # Analytics: attendance, dropout, revenue
│   │   ├── ClassesController.php
│   │   ├── StudentsController.php
│   │   ├── AttendanceController.php
│   │   ├── CalendarController.php             # FullCalendar JSON API
│   │   ├── FinanceController.php
│   │   ├── EnrollmentController.php
│   │   ├── LeadsController.php                # CRM pipeline + convert
│   │   ├── CashbookController.php
│   │   ├── PayrollController.php              # Auto-calc + approve workflow
│   │   ├── TransferController.php             # Chuyển lớp học sinh
│   │   ├── ExportController.php               # CSV/JSON export
│   │   ├── AuditLogController.php             # Audit log viewer
│   │   ├── DispatchRequestController.php      # Cross-tenant approval
│   │   └── StudentPortalController.php        # Student/Parent self-service
│   └── Middleware/
│       ├── EnsureTenantIsSet.php
│       └── RoleMiddleware.php
├── Models/                                    # 12+ models, tất cả BelongsToTenant
├── Services/SessionGeneratorService.php
├── Traits/
│   ├── BelongsToTenant.php                    # Multi-tenancy global scope
│   └── HasAuditLog.php                        # Auto audit log hooks
│
resources/views/
├── layouts/app.blade.php                      # Role-aware sidebar
├── admin/                                     # Super Admin panel (9 views)
├── portal/                                    # Student/Parent portal (4 views)
│   ├── index.blade.php
│   ├── attendance.blade.php
│   ├── invoices.blade.php
│   └── no-student.blade.php
├── calendar/index.blade.php                   # FullCalendar TKB
├── cashbook/index.blade.php
├── leads/index.blade.php
├── payroll/                                   # List + form + detail
├── dashboard.blade.php                        # KPI + analytics + chart
└── ...                                        # 30+ views tổng cộng
│
database/
├── migrations/                                # 12+ migrations
└── seeders/DatabaseSeeder.php
│
routes/
├── web.php                                    # Role guards + portal routes
└── auth.php
```

---

## 🔐 Phân quyền

| Route Group | Roles được phép |
|-------------|----------------|
| Dashboard | tất cả |
| Classes (xem) | tất cả |
| Classes (sửa/xóa) | center_manager, super_admin |
| Attendance | teacher, center_manager, super_admin |
| Students / CRM | center_manager, accountant, super_admin |
| Finance | accountant, center_manager, super_admin |
| Leads / Cashbook / Payroll | accountant, center_manager, super_admin |
| Calendar | tất cả |
| **Portal** | **student, parent** |
| **Admin Panel** | **super_admin only** |

---

## 🚀 Scheduler Setup cho Production

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

---

## 📌 Backlog (Phase 14+)

- [ ] Email/Zalo notification cho học phí đến hạn
- [ ] Transfer lớp full UI (form chuyển lớp giữa chừng, migration đã có)
- [ ] API endpoints cho mobile app
- [ ] Điểm bài tập / Điểm thi nâng cao (quiz online)
- [ ] Bảo lưu tự động hết hạn (cron job)

---

## 📄 PDF Export (Phase 13)

URL prefix: `/pdf/` → Role: `center_manager`, `accountant`, `super_admin`

| Endpoint | Mô tả |
|----------|--------|
| `GET /pdf/students/{student}` | Hồ sơ HS (A4 portrait) |
| `GET /pdf/invoices/{invoice}` | Phiếu thu học phí (A5) |
| `GET /pdf/payroll?month=YYYY-MM` | Bảng lương tháng (A4 landscape) |
| `GET /pdf/classes/{classroom}` | Báo cáo điểm danh lớp (A4 landscape) |

## 📊 Bảng Điểm (Phase 13)

| Endpoint | Mô tả |
|----------|--------|
| `GET /classes/{classroom}/grades` | Ma trận điểm lớp (students × sessions) |
| `GET /students/{student}/grades` | Điểm cá nhân theo enrollment |

## ⏸️ Bảo Lưu Khóa Học (Phase 13)

| Endpoint | Mô tả |
|----------|--------|
| `GET /enrollments/{enrollment}/reserve` | Form bảo lưu |
| `POST /enrollments/{enrollment}/reserve` | Xác nhận bảo lưu |
| `PATCH /enrollments/{enrollment}/reactivate` | Kích hoạt lại |

Migration: `reserved_at`, `reservation_ends_at`, `reservation_note` trên `enrollments` table.
