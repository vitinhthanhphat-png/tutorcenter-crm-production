# TutorCenter CRM — Report Code
> Sinh tự động ngày 18/03/2026 | Phase 10 — Requirements Completion

---

## ✅ Tổng quan thực hiện

| Phase | Nội dung | Trạng thái |
|-------|---------|------------|
| 1–8   | Database, Backend, Frontend, Security, Sessions, RBAC, Demo, Super Admin | ✅ DONE |
| 9     | Multi-Tenant Staff Assignment (migrations, DispatchRequest, Assignments, sidebar) | ✅ DONE |
| 10    | CRM Leads, Cashbook, Payroll, Transfer tracking | ✅ DONE |

---

## 📦 Phase 10 — Chi tiết triển khai

### 3.1 CRM Leads Module
| Loại | File | Nội dung |
|------|------|---------|
| Migration | `create_leads_table.php` | 6 trạng thái pipeline, source, follow_up, conversion link |
| Model | `app/Models/Lead.php` | BelongsToTenant, `statuses()`, `statusLabel()`, `statusColor()`, scopePending |
| Controller | `app/Http/Controllers/LeadsController.php` | Full CRUD + `convert()` → tạo Student từ Lead |
| View | `leads/index.blade.php` | Pipeline summary cards (6 badges), filter, table với convert+delete |
| View | `leads/form.blade.php` | Create/edit form 2 cột |
| View | `leads/show.blade.php` | Detail view + Convert button |
| Routes | 8 routes (`leads.*`) under `role:center_manager,operations,accountant,super_admin` |

### 3.3 Cashbook (Sổ Thu Chi)
| Loại | File | Nội dung |
|------|------|---------|
| Migration | `create_cashbook_table.php` | income/expense, category, amount, reference, recorded_by |
| Model | `app/Models/Cashbook.php` | `categories()` static, BelongsToTenant, recorder relationship |
| Controller | `app/Http/Controllers/CashbookController.php` | index+filter+totals, store, destroy |
| View | `cashbook/index.blade.php` | KPI cards (thu/chi/cân đối) + split panel: form bên trái, bảng bên phải |
| Routes | 3 routes (`cashbook.*`) under `role:accountant,center_manager,super_admin` |

### 3.3 Payroll (Bảng Lương)
| Loại | File | Nội dung |
|------|------|---------|
| Migration | `create_payrolls_table.php` | user×month×tenant unique, session/hour/base rates, workflow status |
| Model | `app/Models/Payroll.php` | `calculateTotal()`, `statusLabel()`, `statusColor()`, draft→confirmed→paid |
| Controller | `app/Http/Controllers/PayrollController.php` | index, generate (tính từ ClassSession data), confirm, markPaid, destroy |
| View | `payroll/index.blade.php` | Month picker + generate form bên trái + bảng payroll bên phải |
| Routes | 5 routes (`payroll.*`) under `role:center_manager,accountant,super_admin` |

### 3.2 Student Transfer Tracking
| Loại | File | Nội dung |
|------|------|---------|
| Migration | `add_transfer_log_to_enrollments_table.php` | transferred_from/to, timestamp, credit_balance, transfer_note |

---

## 🔄 Cập nhật Sidebar Nav (layouts/app.blade.php)

| Mục mới | Route | Role access |
|---------|-------|-------------|
| **CRM Leads** | `leads.index` | operations, accountant, center_manager, super_admin |
| **Sổ Thu Chi** | `cashbook.index` | accountant, center_manager, super_admin |
| **Bảng Lương** | `payroll.index` | accountant, center_manager, super_admin |

---

## 🏗️ Kiến trúc Features Mới

```
Luồng CRM:
  Lead (new) → contacted → consulting → test_booked → registered
                                                         ↓
                                                   Student tạo tự động
                                                   via leads.convert

Luồng Payroll:
  Tháng N chọn → Chọn GV → Hệ thống đọc ClassSession count
  → Generate draft payroll → Quản lý xác nhận → Kế toán đánh dấu đã TT

Luồng Cashbook:
  Ghi income/expense hàng ngày → Tổng hợp tháng → Cân đối thu-chi
```

---

## 🧪 Self-Test Results

| Test Case | Kết quả |
|-----------|---------|
| `php artisan migrate` — 4 new migrations | ✅ Thành công |
| `php -l LeadsController.php` | ✅ No syntax errors |
| `php -l CashbookController.php` | ✅ No syntax errors |
| `php -l PayrollController.php` | ✅ No syntax errors |
| `php artisan route:list --name=leads` | ✅ 8 routes registered |
| `php artisan route:list --name=cashbook` | ✅ 3 routes registered |
| `php artisan route:list --name=payroll` | ✅ 5 routes registered |
| `npm run build` | ✅ Built in 699ms |
| `BelongsToTenant` scope multi-tenant | ✅ Confirmed (Phase 9) |

---

## 🗺️ Mapping requirements.md → Implementation

| Yêu cầu | Status |
|---------|--------|
| **3.1** CRM Leads — pipeline + source + follow-up | ✅ Implemented |
| **3.1** Chuyển Lead → Student | ✅ `leads.convert` action |
| **3.2** Quản lý Lớp học | ✅ Phases 2–3 |
| **3.2** TKB Calendar | ⚠️ Dashboard shows today's schedule; full calendar view is future work |
| **3.2** Điểm danh & Đánh giá | ✅ Phase 5 |
| **3.2** Luân chuyển Lớp (Transfer) | ✅ Migration columns added in Phase 10 |
| **3.3** Học phí & Bán hàng | ✅ EnrollmentController + InvoiceController |
| **3.3** Cashbook | ✅ Implemented |
| **3.3** Payroll (lương theo buổi/giờ) | ✅ Implemented |
| **Roles 1–6** Super Admin, Manager, Accountant, Teacher | ✅ RBAC via RoleMiddleware |
| **Multi-tenant data isolation** | ✅ BelongsToTenant trait + staff_assignments |

---

## 📌 Còn lại (Future / Phase 11+)

| Feature | Ghi chú |
|---------|---------|
| Full Calendar view | Hiển thị TKB trực quan theo tuần/tháng |
| Export Excel/PDF | Báo cáo lương, học phí, sổ thu chi |
| Email/Zalo thông báo | Gửi thông báo học phí đến hạn |
| Transfer lớp UI | Form UI để thực hiện chuyển lớp (migration đã có) |
| Parent/Student portal | Web portal xem lịch học, điểm danh |
| Audit log | Ghi lại ai thay đổi gì trong Super Admin |
