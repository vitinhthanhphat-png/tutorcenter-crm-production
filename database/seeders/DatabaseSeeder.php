<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    private int $invoiceSeq = 1;

    // ─────────────── Vietnamese name banks ───────────────
    private array $ho = ['Nguyễn', 'Trần', 'Lê', 'Phạm', 'Hoàng', 'Huỳnh', 'Võ', 'Đặng', 'Bùi', 'Đỗ', 'Hồ', 'Ngô', 'Dương', 'Lý', 'Vũ'];
    private array $dem = ['Văn', 'Thị', 'Thanh', 'Minh', 'Quốc', 'Hữu', 'Phương', 'Gia', 'Ngọc', 'Đình', 'Anh', 'Khánh', 'Thiên', 'Bảo', 'Tường'];
    private array $ten = ['An', 'Bình', 'Chi', 'Duy', 'Em', 'Giang', 'Hải', 'Khôi', 'Linh', 'Minh', 'Nhi', 'Phúc', 'Quân', 'Sơn', 'Tâm', 'Uyên', 'Vi', 'Xuân', 'Yến', 'Anh', 'Đạt', 'Huy', 'Khanh', 'Long', 'Mai', 'Nam', 'Phong', 'Quang', 'Thắng', 'Tuấn', 'Vân', 'Hà', 'Trung', 'Kiệt', 'Thảo', 'Tú', 'Hương', 'Ngân', 'Trang', 'Đức'];

    private array $schools = ['THPT Nguyễn Thượng Hiền', 'THPT Lê Hồng Phong', 'THPT Gia Định', 'THPT Trần Đại Nghĩa', 'THPT Marie Curie', 'TH Phan Đình Phùng', 'THCS Trần Văn Ơn', 'THPT Nguyễn Khuyến', 'THPT Bùi Thị Xuân', 'THPT Hùng Vương'];
    private array $leadSources = ['facebook', 'zalo', 'referral', 'website', 'tiktok', 'walk_in'];
    private int $nameIdx = 0;

    public function run(): void
    {
        // ══════════════════════════════════════════════════════
        // SUPER ADMIN (xuyên tenant)
        // ══════════════════════════════════════════════════════
        User::create([
            'name' => 'Super Admin', 'email' => 'admin@tutorcenter.vn',
            'password' => Hash::make('SuperAdmin@2026'), 'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // ══════════════════════════════════════════════════════
        // 3 TENANTS
        // ══════════════════════════════════════════════════════
        $tenants = [
            [
                'name' => 'Trung tâm Gia sư Ánh Dương',
                'domain' => 'anhduong.tutorcenter.vn',
                'email_prefix' => 'anhduong',
                'branches' => [
                    ['name' => 'CS Quận 1',  'address' => '123 Nguyễn Huệ, Q.1, HCM',  'phone' => '028.1234.5678'],
                    ['name' => 'CS Quận 7',  'address' => '45 Nguyễn Thị Thập, Q.7, HCM', 'phone' => '028.9876.0001'],
                    ['name' => 'CS Thủ Đức', 'address' => '88 Phạm Văn Đồng, TP.Thủ Đức', 'phone' => '028.3344.5566'],
                ],
                'courses' => [
                    ['name' => 'IELTS 6.0 General',    'subject' => 'English', 'sessions' => 60, 'price' => 4500000],
                    ['name' => 'IELTS 7.0 Academic',   'subject' => 'English', 'sessions' => 80, 'price' => 6800000],
                    ['name' => 'Toán Nâng Cao 11',     'subject' => 'Math',    'sessions' => 45, 'price' => 3200000],
                    ['name' => 'Lý Nâng Cao 12',       'subject' => 'Physics', 'sessions' => 40, 'price' => 3000000],
                    ['name' => 'Lập trình Python',     'subject' => 'IT',      'sessions' => 30, 'price' => 2800000],
                    ['name' => 'TOEIC 700+',           'subject' => 'English', 'sessions' => 50, 'price' => 3900000],
                ],
                'num_classes' => 8,
            ],
            [
                'name' => 'Học viện Ngôi Sao Sáng',
                'domain' => 'ngoisaosang.tutorcenter.vn',
                'email_prefix' => 'star',
                'branches' => [
                    ['name' => 'CS Hà Đông',    'address' => '12 Quang Trung, Hà Đông, HN',   'phone' => '024.6789.1234'],
                    ['name' => 'CS Cầu Giấy',   'address' => '99 Xuân Thủy, Cầu Giấy, HN',    'phone' => '024.5555.6666'],
                ],
                'courses' => [
                    ['name' => 'Toán Olympiad',     'subject' => 'Math',    'sessions' => 50, 'price' => 5500000],
                    ['name' => 'Hóa Nâng Cao',     'subject' => 'Chemistry','sessions' => 40, 'price' => 3600000],
                    ['name' => 'Anh Văn Giao Tiếp','subject' => 'English',  'sessions' => 36, 'price' => 2800000],
                    ['name' => 'Tiếng Hàn Sơ Cấp', 'subject' => 'Korean',   'sessions' => 30, 'price' => 2500000],
                    ['name' => 'Tin Học Văn Phòng', 'subject' => 'IT',       'sessions' => 24, 'price' => 1800000],
                ],
                'num_classes' => 7,
            ],
            [
                'name' => 'TT Giáo dục Tương Lai',
                'domain' => 'tuonglai.tutorcenter.vn',
                'email_prefix' => 'future',
                'branches' => [
                    ['name' => 'CS Ninh Kiều',  'address' => '50 Đại lộ Hòa Bình, NK, Cần Thơ', 'phone' => '0292.123.4567'],
                    ['name' => 'CS Bình Thủy',  'address' => '11 CMT8, Bình Thủy, Cần Thơ',     'phone' => '0292.999.8888'],
                ],
                'courses' => [
                    ['name' => 'Toán THPT Quốc gia',   'subject' => 'Math',    'sessions' => 55, 'price' => 3500000],
                    ['name' => 'Văn Nghị luận',         'subject' => 'Literature','sessions' => 40, 'price' => 2800000],
                    ['name' => 'Anh Văn THCS',          'subject' => 'English',   'sessions' => 36, 'price' => 2200000],
                    ['name' => 'Anh Văn THPT',          'subject' => 'English',   'sessions' => 48, 'price' => 3200000],
                ],
                'num_classes' => 6,
            ],
        ];

        foreach ($tenants as $tData) {
            $this->seedTenant($tData);
        }
    }

    private function seedTenant(array $conf): void
    {
        $prefix = $conf['email_prefix'];

        // ── 1. Tenant ──
        $tenant = Tenant::create([
            'name'   => $conf['name'],
            'domain' => $conf['domain'],
            'status' => 'active',
        ]);
        $tid = $tenant->id;

        // ── 2. Branches ──
        $branches = [];
        foreach ($conf['branches'] as $bd) {
            $branches[] = Branch::create(array_merge($bd, ['tenant_id' => $tid]));
        }

        // ── 3. Users: 3 managers + 6 teachers + 10 tutors + 2 accountants ──
        $pw = Hash::make('Demo@2026');

        $managers = [];
        for ($i = 1; $i <= 3; $i++) {
            $managers[] = User::create([
                'name' => $this->vnName(), 'email' => "manager{$i}@{$prefix}.vn",
                'password' => $pw, 'role' => 'center_manager',
                'tenant_id' => $tid, 'branch_id' => $branches[($i - 1) % count($branches)]->id,
                'email_verified_at' => now(),
            ]);
        }

        $teachers = [];
        for ($i = 1; $i <= 6; $i++) {
            $teachers[] = User::create([
                'name' => $this->vnName(), 'email' => "teacher{$i}@{$prefix}.vn",
                'password' => $pw, 'role' => 'teacher',
                'tenant_id' => $tid, 'branch_id' => $branches[($i - 1) % count($branches)]->id,
                'email_verified_at' => now(),
            ]);
        }

        $tutors = [];
        for ($i = 1; $i <= 10; $i++) {
            $tutors[] = User::create([
                'name' => $this->vnName(), 'email' => "tutor{$i}@{$prefix}.vn",
                'password' => $pw, 'role' => 'tutor',
                'tenant_id' => $tid, 'branch_id' => $branches[($i - 1) % count($branches)]->id,
                'email_verified_at' => now(),
            ]);
        }

        $accountants = [];
        for ($i = 1; $i <= 2; $i++) {
            $accountants[] = User::create([
                'name' => $this->vnName(), 'email' => "accountant{$i}@{$prefix}.vn",
                'password' => $pw, 'role' => 'accountant',
                'tenant_id' => $tid, 'branch_id' => $branches[($i - 1) % count($branches)]->id,
                'email_verified_at' => now(),
            ]);
        }

        // ── 4. Courses ──
        $courses = [];
        foreach ($conf['courses'] as $cd) {
            $courses[] = Course::create([
                'tenant_id'      => $tid,
                'name'           => $cd['name'],
                'subject'        => $cd['subject'],
                'total_sessions' => $cd['sessions'],
                'price'          => $cd['price'],
                'is_active'      => true,
            ]);
        }

        // ── 5. Classes ──
        $schedules = [
            ['days' => ['monday', 'wednesday', 'friday'],    'start_time' => '17:00', 'end_time' => '19:00'],
            ['days' => ['tuesday', 'thursday', 'saturday'],  'start_time' => '18:00', 'end_time' => '20:00'],
            ['days' => ['monday', 'wednesday'],              'start_time' => '19:00', 'end_time' => '21:00'],
            ['days' => ['tuesday', 'thursday'],              'start_time' => '17:30', 'end_time' => '19:30'],
            ['days' => ['saturday', 'sunday'],               'start_time' => '08:00', 'end_time' => '10:30'],
            ['days' => ['saturday', 'sunday'],               'start_time' => '14:00', 'end_time' => '16:30'],
            ['days' => ['monday', 'wednesday', 'friday'],    'start_time' => '08:00', 'end_time' => '10:00'],
            ['days' => ['tuesday', 'thursday', 'saturday'],  'start_time' => '19:30', 'end_time' => '21:30'],
        ];
        $rooms = ['P.101', 'P.102', 'P.103', 'P.201', 'P.202', 'P.203', 'Lab 01', 'Lab 02', 'P.301', 'P.302'];
        $classNames = ['K12A', 'K11B', 'K10C', 'Q7-01', 'Q1-02', 'NC-A', 'NC-B', 'SC-01', 'VIP-01', 'S-01'];

        $classes = [];
        $numClasses = $conf['num_classes'];
        for ($i = 0; $i < $numClasses; $i++) {
            $course  = $courses[$i % count($courses)];
            $teacher = $teachers[$i % count($teachers)];
            $tutor   = $tutors[$i % count($tutors)];
            $branch  = $branches[$i % count($branches)];
            $sched   = $schedules[$i % count($schedules)];
            $status  = $i < $numClasses - 1 ? 'active' : 'planned';

            $classes[] = ClassRoom::create([
                'tenant_id'     => $tid,
                'branch_id'     => $branch->id,
                'course_id'     => $course->id,
                'teacher_id'    => $teacher->id,
                'tutor_id'      => $tutor->id,
                'name'          => mb_substr($course->name, 0, 12) . ' - ' . $classNames[$i % count($classNames)],
                'room_name'     => $rooms[$i % count($rooms)],
                'schedule_rule' => $sched,
                'start_date'    => '2026-02-01',
                'max_students'  => rand(12, 20),
                'status'        => $status,
            ]);
        }

        // ── 6. Students: 10 per class ──
        $allStudents = [];
        $studentIdx = 0;
        foreach ($classes as $cls) {
            for ($s = 0; $s < 10; $s++) {
                $isStudying = $s < 8;
                $student = Student::create([
                    'tenant_id'   => $tid,
                    'branch_id'   => $cls->branch_id,
                    'name'        => $this->vnName(),
                    'phone'       => '09' . str_pad((string) rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                    'dob'         => Carbon::create(rand(2005, 2012), rand(1, 12), rand(1, 28)),
                    'school'      => $this->schools[array_rand($this->schools)],
                    'status'      => $isStudying ? 'studying' : ($s === 8 ? 'lead' : 'dropped'),
                    'lead_source' => $this->leadSources[array_rand($this->leadSources)],
                    'lead_status' => $isStudying ? 'confirmed' : ($s === 8 ? 'contacted' : null),
                    'notes'       => $isStudying ? null : ($s === 8 ? 'Phụ huynh đang cân nhắc' : 'Nghỉ vì lý do cá nhân'),
                ]);
                $allStudents[] = ['student' => $student, 'class' => $cls];
                $studentIdx++;
            }
        }

        // ── 6b. Portal Users: link first 2 students to User accounts ──
        $portalStudents = array_slice($allStudents, 0, 2);
        foreach ($portalStudents as $idx => $item) {
            $stu = $item['student'];
            $portalUser = User::create([
                'name'     => $stu->name,
                'email'    => 'student' . ($idx + 1) . '@' . $prefix . '.vn',
                'password' => Hash::make('Demo@2026'),
                'role'     => 'student',
                'tenant_id' => $tid,
            ]);
            $stu->update(['user_id' => $portalUser->id]);
        }

        // ── 7. Enrollments + Invoices ──
        $this->invoiceSeq = 1;
        foreach ($allStudents as $item) {
            $stu = $item['student'];
            $cls = $item['class'];
            if ($stu->status !== 'studying') continue;

            $price = $cls->course?->price ?? 3000000;
            $discount = rand(0, 3) === 0 ? rand(200000, 500000) : 0;
            $finalPrice = $price - $discount;
            $paidRatio = [1.0, 1.0, 1.0, 0.5, 0.7, 1.0, 0.3][array_rand([1.0, 1.0, 1.0, 0.5, 0.7, 1.0, 0.3])];
            $paid = (int) round($finalPrice * $paidRatio / 1000) * 1000;

            $enr = Enrollment::create([
                'tenant_id'   => $tid,
                'student_id'  => $stu->id,
                'class_id'    => $cls->id,
                'final_price' => $finalPrice,
                'paid_amount' => $paid,
                'status'      => 'active',
                'enrolled_by' => $managers[0]->id,
                'discount_note' => $discount > 0 ? ('Giảm ' . number_format($discount) . 'đ') : null,
            ]);

            // Invoice
            if ($paid > 0) {
                $methods = ['cash', 'transfer', 'transfer', 'card'];
                $code = 'PT-' . now()->format('ym') . '-' . str_pad((string) $this->invoiceSeq++, 4, '0', STR_PAD_LEFT);
                Invoice::create([
                    'tenant_id'        => $tid,
                    'enrollment_id'    => $enr->id,
                    'cashier_id'       => $accountants[array_rand($accountants)]->id,
                    'invoice_code'     => $code,
                    'amount'           => $paid,
                    'payment_method'   => $methods[array_rand($methods)],
                    'transaction_date' => now()->subDays(rand(1, 30))->toDateString(),
                    'notes'            => 'Học phí kỳ ' . now()->format('m/Y'),
                ]);
            }
        }

        // ── 8. Sessions (Feb + Mar 2026) + Attendance ──
        foreach ($classes as $cls) {
            if ($cls->status === 'planned') continue;
            $this->generateSessions($tid, $cls, $cls->teacher_id);
            $this->seedAttendance($cls);
        }

        // ── 9. CRM Leads ──
        $leadStatuses  = ['new', 'contacted', 'consulting', 'test_booked'];
        $leadSources   = ['Facebook', 'Zalo', 'Website', 'Giới thiệu', 'Tờ rơi', 'TikTok'];
        $intCourses    = array_column($conf['courses'], 'name');
        for ($i = 0; $i < 8; $i++) {
            Lead::create([
                'tenant_id'         => $tid,
                'branch_id'         => $branches[$i % count($branches)]->id,
                'name'              => $this->vnName(),
                'phone'             => '09' . str_pad((string) rand(10000000, 99999999), 8, '0', STR_PAD_LEFT),
                'email'             => 'lead' . ($i + 1) . '@' . $prefix . '.vn',
                'parent_name'       => $i % 3 === 0 ? $this->vnName() : null,
                'status'            => $leadStatuses[$i % count($leadStatuses)],
                'source'            => $leadSources[array_rand($leadSources)],
                'interested_course' => $intCourses[array_rand($intCourses)],
                'note'              => $i % 2 === 0 ? 'Quan tâm khóa học, cần tư vấn thêm' : null,
                'assigned_to'       => $managers[$i % count($managers)]->id,
                'follow_up_at'      => now()->addDays(rand(1, 14))->toDateString(),
            ]);
        }
    }

    // ─────────────── Helpers ───────────────

    private function vnName(): string
    {
        $name = $this->ho[array_rand($this->ho)] . ' '
              . $this->dem[array_rand($this->dem)] . ' '
              . $this->ten[$this->nameIdx % count($this->ten)];
        $this->nameIdx++;
        return $name;
    }

    private function generateSessions(int $tid, ClassRoom $cls, ?int $teacherId): void
    {
        $rule = $cls->schedule_rule;
        if (empty($rule['days'])) return;

        $dayMap = ['monday' => 1, 'tuesday' => 2, 'wednesday' => 3, 'thursday' => 4, 'friday' => 5, 'saturday' => 6, 'sunday' => 0];
        $targetDays = array_filter(array_map(fn($d) => $dayMap[strtolower($d)] ?? null, $rule['days']));

        $from = '2026-02-01';
        $to   = '2026-03-31';
        $period = CarbonPeriod::create($from, $to);

        foreach ($period as $date) {
            if (!in_array($date->dayOfWeek, $targetDays)) continue;
            if (ClassSession::where('class_id', $cls->id)->where('date', $date->toDateString())->exists()) continue;

            ClassSession::create([
                'tenant_id'  => $tid,
                'class_id'   => $cls->id,
                'teacher_id' => $teacherId,
                'date'       => $date->toDateString(),
                'start_time' => $rule['start_time'],
                'end_time'   => $rule['end_time'],
                'type'       => 'regular',
                'status'     => $date->isPast() ? 'completed' : 'scheduled',
            ]);
        }
    }

    private function seedAttendance(ClassRoom $cls): void
    {
        $enrolled = $cls->enrollments()->get();
        if ($enrolled->isEmpty()) return;

        $completedSessions = ClassSession::where('class_id', $cls->id)
            ->where('status', 'completed')
            ->take(10)
            ->get();

        $statuses = ['present', 'present', 'present', 'present', 'present', 'absent_with_leave', 'late', 'absent_no_leave'];
        $grades   = [null, null, 7, 8, 9, 6.5, 10, 7.5, 8.5, null];

        foreach ($completedSessions as $session) {
            foreach ($enrolled as $enr) {
                Attendance::firstOrCreate([
                    'session_id' => $session->id,
                    'student_id' => $enr->student_id,
                ], [
                    'tenant_id'       => $session->tenant_id,
                    'status'          => $statuses[array_rand($statuses)],
                    'grade'           => $grades[array_rand($grades)],
                    'teacher_comment' => rand(0, 3) === 0 ? $this->randomComment() : null,
                ]);
            }
        }
    }

    private function randomComment(): string
    {
        $comments = [
            'Tập trung tốt, làm bài đầy đủ',
            'Cần ôn lại bài trước',
            'Tiến bộ rõ rệt so với tuần trước',
            'Chưa làm bài tập về nhà',
            'Tham gia sôi nổi, phát biểu nhiều',
            'Nên đọc thêm tài liệu bổ trợ',
            'Hiểu bài nhanh, hỗ trợ bạn học yếu',
            'Cần cải thiện phần nghe',
            'Viết essay tốt hơn tuần trước',
            'Đi muộn 10 phút, nhắc nhở lần 2',
            'Trả lời câu hỏi chính xác',
            'Cần luyện thêm phần speaking',
        ];
        return $comments[array_rand($comments)];
    }
}
