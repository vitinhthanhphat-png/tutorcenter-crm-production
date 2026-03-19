# TutorCenter CRM — Test Plan (Role-based)

> **Ngày tạo:** 19/03/2026 | **Ngày test hoàn thành:** 19/03/2026
> **App URL:** `http://127.0.0.1:8000`
> **Kết quả tổng:** ✅ **10 bugs phát hiện → 10/10 đã fix**

---

## 🔑 Tài khoản demo

| Role | Email | Password | Tenant |
|------|-------|----------|--------|
| Super Admin | `admin@tutorcenter.vn` | `SuperAdmin@2026` | Xuyên tenant |
| Manager (T1) | `manager1@anhduong.vn` | `Demo@2026` | Ánh Dương |
| Teacher (T1) | `teacher1@anhduong.vn` | `Demo@2026` | Ánh Dương |
| Tutor (T1) | `tutor1@anhduong.vn` | `Demo@2026` | Ánh Dương |
| Accountant (T1) | `accountant1@anhduong.vn` | `Demo@2026` | Ánh Dương |
| **Student (T1)** | `student1@anhduong.vn` | `Demo@2026` | Ánh Dương |
| Manager (T2) | `manager1@star.vn` | `Demo@2026` | Ngôi Sao Sáng |
| Manager (T3) | `manager1@future.vn` | `Demo@2026` | Tương Lai |

---

## 1️⃣ SUPER ADMIN — ✅ Pass

- [x] Login → Dashboard ✅ (sau fix bugs #1-3)
- [x] Admin Panel `/admin` — 3 tenants, 7 branches, 63 users, 210 HS ✅
- [x] Tenants CRUD ✅
- [x] Users Management (64 accounts) ✅
- [x] Branches (7) ✅
- [x] Audit Log ✅ (sau fix bug #4)
- [x] Settings ✅

## 2️⃣ CENTER MANAGER — ✅ Pass

- [x] Dashboard — KPI: 64 HS, 7 lớp, 165M VNĐ, 67.2% attendance ✅
- [x] Students `/students` ✅
- [x] Classes `/classes` — 8 lớp ✅
- [x] CRM Leads `/leads` — 8 leads ✅ (sau fix seeder)
- [x] Finance `/finance/invoices` ✅
- [x] Cashbook ✅
- [x] Payroll ✅
- [x] Calendar ✅
- [x] Export ✅
- [x] Sidebar đủ menu ✅

## 3️⃣ TEACHER — ✅ Pass

- [x] Dashboard ✅
- [x] Classes (lớp mình dạy) ✅
- [x] Calendar ✅
- [x] `/students` → 🚫 403 ✅
- [x] `/finance` → 🚫 403 ✅
- [x] `/leads` → 🚫 403 ✅
- [x] `/admin` → 🚫 403 ✅

## 4️⃣ TUTOR — ✅ Pass

- [x] Dashboard ✅
- [x] Classes (lớp mình trợ giảng) ✅
- [x] Calendar ✅
- [x] `/students` → 🚫 403 ✅
- [x] `/finance` → 🚫 403 ✅
- [x] `/leads` → 🚫 403 ✅
- [x] `/admin` → 🚫 403 ✅

## 5️⃣ ACCOUNTANT — ✅ Pass

- [x] Dashboard ✅
- [x] Students ✅
- [x] Finance ✅
- [x] Cashbook ✅
- [x] Payroll ✅
- [x] `/admin` → 🚫 403 ✅
- [x] `/classes/create` → 🚫 403 ✅ (sau fix route order)

## 6️⃣ STUDENT PORTAL — ✅ Pass

- [x] `/portal` — lịch học, điểm danh, thanh toán ✅ (sau fix controller)
- [x] `/portal/attendance` — lịch sử điểm danh ✅
- [x] `/portal/invoices` — lịch sử thanh toán ✅
- [x] `/admin` → 🚫 403 ✅
- [x] `/students` → 🚫 403 ✅

## 7️⃣ MULTI-TENANT ISOLATION — ✅ Pass

- [x] T1 (Ánh Dương): 64 HS, 8 lớp ✅
- [x] T2 (Ngôi Sao Sáng): 56 HS, 7 lớp ✅
- [x] Data completely isolated ✅

---

## ✏️ Bug Log (10 bugs — ALL FIXED)

| # | Severity | File | Bug | Status |
|---|----------|------|-----|--------|
| 1 | Critical | DashboardController:48 | FK `class_session_id` → `session_id` | ✅ |
| 2 | Critical | DashboardController:54 | Enum `absent` not found | ✅ |
| 3 | Critical | DashboardController:91 | `invoices.status`/`due_date` missing | ✅ |
| 4 | Critical | audit/index.blade.php | `@match` → `@switch` | ✅ |
| 5 | Medium | routes/web.php | Route order: show before create | ✅ |
| 6 | Low | DatabaseSeeder | `leads` table empty | ✅ |
| 7 | Low | 5 files | `session_date` → `date` | ✅ |
| 8 | Critical | StudentPortalController | `invoices.student_id` missing | ✅ |
| 9 | Critical | StudentPortalController | `invoices.status`/`due_date` missing | ✅ |
| 10 | Medium | portal/index.blade.php | `$unpaidInvoices` + `due_date` ref | ✅ |
