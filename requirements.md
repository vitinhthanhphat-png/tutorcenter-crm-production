# Tài liệu Đặc tả Yêu cầu (Requirements)
## Dự án: Hệ thống Phần mềm Quản lý Trung tâm Gia sư / Đào tạo (SaaS)

> **Mô tả ngắn gọn:** Một nền tảng Web App (SaaS) được xây dựng trên Laravel 13+ dành cho việc quản lý toàn diện các trung tâm gia sư/lớp học thêm, bao gồm CRM nội bộ, vận hành lớp học, và quản lý tài chính/thu phí. Hỗ trợ Multi-tenant (tách biệt dữ liệu nhiều trung tâm trên cùng một mã nguồn).

---

## ✅ Trạng thái Triển khai MVP (Cập nhật 18/03/2026)

| Yêu cầu | Trạng thái | Ghi chú |
|---------|-----------|---------|
| Multi-tenant architecture | ✅ Hoàn thành | `BelongsToTenant` trait, Global Scope |
| Super Admin panel | ✅ Hoàn thành | Tenants, Users, Branches, Assignments, Dispatch, Audit Log, Settings |
| Phân quyền RBAC | ✅ Hoàn thành | 7 roles, `RoleMiddleware`, route guards |
| CRM Leads & pipeline | ✅ Hoàn thành | Lead status, follow-up, convert→Student |
| Quản lý Lớp học | ✅ Hoàn thành | Tạo/sửa/xóa, gán GV, schedule_rule |
| Thời khóa biểu Calendar | ✅ Hoàn thành | FullCalendar, JSON API, filter role |
| Điểm danh | ✅ Hoàn thành | Theo buổi, trạng thái P/A/L/E, ghi chú GV |
| Học phí (Invoice) | ✅ Hoàn thành | Tạo, theo dõi, trạng thái paid/pending/overdue |
| Cashbook (Thu chi) | ✅ Hoàn thành | Income/Expense, KPI, filter category |
| Bảng lương Giáo viên | ✅ Hoàn thành | Tính tự động từ sessions, workflow draft→paid |
| Chuyển lớp (Transfer) | ✅ Hoàn thành | TransferController, migration |
| Export báo cáo | ✅ Hoàn thành | CSV/JSON export, ExportController |
| Cross-tenant staff | ✅ Hoàn thành | staff_assignments, dispatch_requests, approval |
| Student/Parent Portal | ✅ Hoàn thành | `/portal` — lịch học, điểm danh, học phí |
| Audit Log | ✅ Hoàn thành | `HasAuditLog` trait tự động ghi Student/Enrollment/Payroll |
| Dashboard Analytics | ✅ Hoàn thành | Attendance rate, dropout, overdue, revenue trend chart |
| **PDF Export** | 🔄 Backlog | DomPDF PHP version conflict |
| Email/Zalo thông báo | 🔄 Backlog | Phase 13+ |
| Mobile API | 🔄 Backlog | Phase 13+ |

---

## 1. Nền tảng & Kiến trúc Kỹ thuật (Technical Architecture)
- **Backend Framework:** Laravel 13+ (trở lên), PHP 8.2+.
- **Frontend Stack:** Laravel + Livewire + Tailwind CSS (Phân phối qua trình duyệt Web, chưa có Native App).
- **Cơ sở dữ liệu (Database):** MySQL / PostgreSQL.
- **Kiến trúc Dữ liệu:** **Multi-tenant (Cô lập dữ liệu theo Trung tâm/Chi nhánh).** Dữ liệu của chi nhánh nào chỉ hiển thị cho nhân viên chi nhánh đó. Cơ chế: Sử dụng `tenant_id` (hoặc `center_id` / `branch_id`) trên hầu hết các bảng dữ liệu cốt lõi để query scope.

---

## 2. Đối tượng Người dùng & Phân quyền (Roles & Permissions)

Hệ thống có các Roles chính sau, phân chia theo tính chất hiển thị dữ liệu (Data Visibility):

1. **Super Admin (Cấp cao nhất):**
   - Quản trị viên của toàn bộ hệ thống SaaS.
   - Quản lý các Trung tâm (Tạo tài khoản trung tâm). Giai đoạn MVP chưa cần tính năng thiết lập gói cước/gia hạn.
2. **Quản lý Trung tâm / Chủ doanh nghiệp (Center Manager / Owner):**
   - Xem toàn bộ dữ liệu của trung tâm mình (tất cả các chi nhánh).
   - Được cấu hình các thông số: Mức thu học phí, loại khóa học, chương trình học.
3. **Quản lý Chi nhánh (Branch Manager):**
   - Chỉ được xem và điều hành dữ liệu thuộc Chi nhánh (Branch) được phân công.
   - Quản lý tổng thể mọi hoạt động của chi nhánh đó (nhân sự, tài chính, lớp học).
4. **Vận hành Chi nhánh (Branch Operations / Lễ tân):**
   - Thực hiện các tác vụ điều phối hàng ngày tại chi nhánh.
   - Thêm/sửa điểm danh, xếp lịch dự phòng, hỗ trợ sắp xếp phòng học, giải đáp phụ huynh.
5. **Kế toán (Accountant):**
   - Nhóm quyền tài chính: Thu học phí, quản lý công nợ, duyệt phiếu chi, duyệt bảng lương.
5. **Giáo viên (Teacher):**
   - Visibility rất hạn chế: **Chỉ được xem danh sách học sinh thuộc lớp mình đang dạy.**
   - Không được xem thông tin liên hệ của Phụ huynh (Bảo mật data khách hàng).
   - Chỉ xem được thời khóa biểu của mình. Điểm danh, nhập điểm/nhận xét cho học sinh.
6. **Trợ giảng (Tutor/TA):**
   - Tương tự giáo viên, hỗ trợ điểm danh, chấm bài.
8. **Phụ huynh (Parent):**
   - Có tài khoản đăng nhập Web Portal riêng (mỗi phụ huynh 1 tài khoản, có thể link với nhiều học sinh con em mình).
   - Xem TKB, xem Điểm danh, nhận xét của Giáo viên, thông báo học phí. Không xem được thông tin của học sinh khác.
9. **Học sinh (Student):**
   - Giống như phụ huynh, nhưng tài khoản cá nhân độc lập.
   - Web portal để xem Lịch học, xem kết quả Điểm bài tập/bài thi, Điểm danh của bản thân.
   - (Tương lai có thể thêm nộp bài tập online, trao đổi bài vở với Giảng viên).

---

## 3. Phạm vi tính năng MVP (Option A + B)

Phiên bản đầu tiên (MVP) sẽ bao gồm tính năng **Vận hành cốt lõi (Core Operations)** kết hợp **Tài chính (Finance)**.

### 3.1. Phân hệ CRM & Quản lý Lead (Cơ bản)
- Quản lý danh sách Khách hàng tiềm năng (Leads).
- Ghi nhận nguồn gốc (Source) và trạng thái chăm sóc (Chưa liên hệ, Đang tư vấn, Hẹn test đầu vào, Đã đăng ký...).
- Chuyển đổi Lead thành Học sinh (Student) khi đóng tiền/chính thức học.

### 3.2. Phân hệ Vận hành Cốt lõi (Core Operations)
- **Quản lý Danh mục:** Khóa học, Môn học, Phòng học.
- **Quản lý Nhân sự:** Giáo viên, Trợ giảng, Nhân viên (Kèm quản lý hợp đồng/thông tin cơ bản).
- **Quản lý Lớp học:** Tạo lớp học, gán Giáo viên/Trợ giảng/Phòng học.
- **Thời khóa biểu (TKB):** 
  - Mô hình **Lịch học cố định** (Fixed Schedule) do Trung tâm thiết lập (Ví dụ: 19h T3-T5).
  - Khả năng tạo ra các buổi học vãng lai/học bù (Make-up classes).
  - Hiển thị TKB theo lịch Calendar trực quan cho từng Role (Quản lý thấy hết, GV thấy lịch của mình).
- **Điểm danh & Đánh giá:** Giáo viên điểm danh từng buổi, ghi chú bài tập/nhận xét.
- **Luân chuyển Lớp (Transfer):** Tính năng **Chuyển lớp** cho học sinh giữa chừng.

### 3.3. Phân hệ Tài chính & Lương (Finance & Payroll)
- **Học phí & Bán hàng:** 
  - Mô hình **Học phí theo Khóa** (Trung tâm tự định nghĩa cấu hình khóa học: VD Khóa IELTS 3 tháng - 50 buổi = 10 triệu).
  - Cho phép tùy biến thêm các khoản thu phụ (Tiền tài liệu, đồng phục...).
  - Quản lý công nợ, thanh toán nhiều lần (Trá góp khóa học).
  - Xử lý tài chính khi "Chuyển lớp", "Bảo lưu" (Cấn trừ tiền thừa/thiếu).
- **Quản lý Thu / Chi tổng hợp (Cashbook):** Ghi nhận các khoản chi phí vận hành chi nhánh (Điện nước, mua sắm).
- **Tính lương Giáo viên / Trợ giảng:** Lương dựa trên dữ liệu báo bài/điểm danh (Lương theo giờ, lương theo buổi, hoặc lương cứng). Lên phiếu tính lương hàng tháng.

---

## 4. Đặc tả Yêu cầu phi chức năng (Non-Functional Requirements)
1. **Bảo mật & Tách biệt dữ liệu:** Đây là yếu tố sống còn do là hệ thống quản lý nhiều trung tâm/chi nhánh. Bắt buộc phải triển khai Global Scopes trên model để đảm bảo an toàn truy xuất tenant.
2. **Giao diện người dùng (UI/UX):** Bố cục giao diện Dashboard thân thiện, Menu điều hướng rõ ràng cho từng Role (Sử dụng các template Admin hiện đại hoặc xây mới với TailwindCSS). Giao diện tối ưu để Kế toán/Lễ tân có thao tác nhanh.
3. **Mở rộng (Scalability):** Hệ thống có khả năng scale đáp ứng hàng chục/trăm điểm danh cùng lúc vào các khung giờ cao điểm đi học buổi tối.

---
> *Tài liệu này được xuất ra từ quá trình Brainstorming. Mọi cấu trúc DB và luồng xử lý kỹ thuật chi tiết sẽ bám sát văn bản này ở bước Implement.*

---

## 👨💻 Tác Giả

**Trần Vĩ Thành** — Freelance Web Developer & Edtech Builder

> Nhận thiết kế web, xây dựng hệ thống cho doanh nghiệp vừa và nhỏ. Chuyên WordPress, WooCommerce, hệ thống quản lý nội bộ và các giải pháp web tùy chỉnh.

| Thông tin | Chi tiết |
|-----------|----------|
| 📱 Điện thoại | [0949.897.293](tel:0949897293) |
| 📧 Email | [thanh.web1001@gmail.com](mailto:thanh.web1001@gmail.com) |
| 🌐 Website | [techsharevn.com](https://techsharevn.com) |
| 📄 CV | [techsharevn.com/my-cv](https://techsharevn.com/my-cv) |
| 📍 Địa chỉ | 47 Tân Hoá, P.14, Q.6, TP.HCM, Việt Nam |

### Kinh Nghiệm

- **2016 – Nay:** Làm việc tại Đạt Minh Wallpaper & Remote tại SR Vietnam
- **Freelance:** Thiết kế website, xây dựng hệ thống quản lý, tư vấn marketing số

### Một Số Dự Án Tiêu Biểu

| Dự án | Link |
|-------|------|
| Custom Order Management (WordPress + WooCommerce) | [Xem dự án](https://techsharevn.com/project/custom-order-management-system-for-stickers-using-wordpress-and-woocommerce) |
| Tim Việc Bảo Hiểm VN | [Xem dự án](https://techsharevn.com/project/tim-viec-bao-hiem-vn) |
| Q2 Legal | [Xem dự án](https://techsharevn.com/project/q2-legal) |
| Dongrealty.com | [Xem dự án](https://techsharevn.com/project/dongrealty-com) |
| Mind Connector VN | [Xem dự án](https://techsharevn.com/project/mind-connector-vn) |
| Life360.vn | [Xem dự án](https://techsharevn.com/project/life360-vn) |
| Quản Lý Kho Đạt Minh | [Xem dự án](https://techsharevn.com/project/quan-ly-kho-dat-minh) |
| SR VietNam New Version | [Xem dự án](https://techsharevn.com/project/sr-vietnam-new-version) |
| TrungTamTuHai.edu.vn | [Xem dự án](https://techsharevn.com/project/trungtamtuhai-edu-vn) |

---

## ☕ Ủng Hộ Dự Án (Donate)

Nếu ứng dụng quản lý trung tâm này hữu ích và bạn muốn ủng hộ tác giả tiếp tục phát triển, bạn có thể donate qua:

### 🏦 Vietcombank

| | |
|---|---|
| **Ngân hàng** | Vietcombank (VCB) |
| **Chủ tài khoản** | Trần Vĩ Thành |
| **Số tài khoản** | `0251 002 668 136` |
| **Nội dung chuyển khoản** | `[Tên bạn] ung ho phan mem quan ly trung tam` |

> 💙 Mọi khoản ủng hộ dù nhỏ đều giúp tác giả có thêm động lực duy trì và phát triển các tính năng mới cho cộng đồng giáo dục Việt Nam. Xin chân thành cảm ơn!
