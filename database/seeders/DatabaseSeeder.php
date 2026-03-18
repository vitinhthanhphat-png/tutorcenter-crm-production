<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Student;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════════════════════
        // 1. TENANT
        // ══════════════════════════════════════════════════════
        $tenant = Tenant::create([
            'name'   => 'Trung tâm Gia sư Ánh Dương',
            'domain' => 'anhduong.tutorcenter.vn',
            'status' => 'active',
        ]);
        $tid = $tenant->id;

        // ══════════════════════════════════════════════════════
        // 2. BRANCHES (2 chi nhánh)
        // ══════════════════════════════════════════════════════
        $b1 = Branch::create(['tenant_id' => $tid, 'name' => 'Cơ sở Quận 1',  'address' => '123 Nguyễn Huệ, Q.1',  'phone' => '028.1234.5678']);
        $b2 = Branch::create(['tenant_id' => $tid, 'name' => 'Cơ sở Quận 7',  'address' => '45 Nguyễn Thị Thập, Q.7', 'phone' => '028.9876.0001']);

        // ══════════════════════════════════════════════════════
        // 3. USERS — tất cả 5 roles
        // ══════════════════════════════════════════════════════
        // Super Admin (xuyên tenant)
        User::create([
            'name' => 'Super Admin', 'email' => 'admin@tutorcenter.vn',
            'password' => Hash::make('password'), 'role' => 'super_admin',
            'email_verified_at' => now(),
        ]);

        // Center Manager
        $mgr = User::create([
            'name' => 'Trần Văn Quản', 'email' => 'manager@anhduong.vn',
            'password' => Hash::make('Demo@2026'), 'role' => 'center_manager',
            'tenant_id' => $tid, 'branch_id' => $b1->id,
            'email_verified_at' => now(),
        ]);

        // Teacher 1
        $t1 = User::create([
            'name' => 'Nguyễn Văn Minh', 'email' => 'teacher1@anhduong.vn',
            'password' => Hash::make('Demo@2026'), 'role' => 'teacher',
            'tenant_id' => $tid, 'branch_id' => $b1->id,
            'email_verified_at' => now(),
        ]);

        // Teacher 2
        $t2 = User::create([
            'name' => 'Phạm Thị Hoa', 'email' => 'teacher2@anhduong.vn',
            'password' => Hash::make('Demo@2026'), 'role' => 'teacher',
            'tenant_id' => $tid, 'branch_id' => $b2->id,
            'email_verified_at' => now(),
        ]);

        // Accountant
        $acct = User::create([
            'name' => 'Lê Thị Kế Toán', 'email' => 'accountant@anhduong.vn',
            'password' => Hash::make('Demo@2026'), 'role' => 'accountant',
            'tenant_id' => $tid, 'branch_id' => $b1->id,
            'email_verified_at' => now(),
        ]);

        // ══════════════════════════════════════════════════════
        // 4. COURSES (3 khóa học)
        // ══════════════════════════════════════════════════════
        $cIelts = Course::create(['tenant_id' => $tid, 'name' => 'IELTS 6.0 General','subject' => 'English','total_sessions' => 60,'price' => 4500000,'is_active' => true]);
        $cMath  = Course::create(['tenant_id' => $tid, 'name' => 'Toán Nâng Cao',     'subject' => 'Math',   'total_sessions' => 45,'price' => 3200000,'is_active' => true]);
        $cCode  = Course::create(['tenant_id' => $tid, 'name' => 'Lập trình Python',  'subject' => 'IT',     'total_sessions' => 30,'price' => 2800000,'is_active' => true]);

        // ══════════════════════════════════════════════════════
        // 5. CLASSES (4 lớp)
        // ══════════════════════════════════════════════════════
        $cls1 = ClassRoom::create([
            'tenant_id' => $tid, 'branch_id' => $b1->id, 'course_id' => $cIelts->id,
            'teacher_id' => $t1->id, 'name' => 'IELTS 6.0 - K12A', 'room_name' => 'P.101',
            'schedule_rule' => ['days' => ['tuesday','thursday','saturday'],'start_time' => '18:00','end_time' => '20:00'],
            'max_students' => 15, 'status' => 'active',
            'start_date' => '2026-02-01',
        ]);

        $cls2 = ClassRoom::create([
            'tenant_id' => $tid, 'branch_id' => $b1->id, 'course_id' => $cMath->id,
            'teacher_id' => $t1->id, 'name' => 'Toán NC - K11B', 'room_name' => 'P.102',
            'schedule_rule' => ['days' => ['monday','wednesday','friday'],'start_time' => '17:00','end_time' => '19:00'],
            'max_students' => 12, 'status' => 'active',
            'start_date' => '2026-02-01',
        ]);

        $cls3 = ClassRoom::create([
            'tenant_id' => $tid, 'branch_id' => $b2->id, 'course_id' => $cCode->id,
            'teacher_id' => $t2->id, 'name' => 'Python - Q7A', 'room_name' => 'Lab 01',
            'schedule_rule' => ['days' => ['saturday','sunday'],'start_time' => '09:00','end_time' => '11:30'],
            'max_students' => 10, 'status' => 'active',
            'start_date' => '2026-03-01',
        ]);

        $cls4 = ClassRoom::create([
            'tenant_id' => $tid, 'branch_id' => $b2->id, 'course_id' => $cIelts->id,
            'teacher_id' => $t2->id, 'name' => 'IELTS 5.5 - K10C', 'room_name' => 'P.201',
            'schedule_rule' => ['days' => ['monday','wednesday'],'start_time' => '19:00','end_time' => '21:00'],
            'max_students' => 15, 'status' => 'planned',
            'start_date' => '2026-04-01',
        ]);

        // ══════════════════════════════════════════════════════
        // 6. STUDENTS (10 học sinh với đủ trạng thái)
        // ══════════════════════════════════════════════════════
        $students = [
            ['Nguyễn Thanh Đạt',  '0901234567', 'q1',  'studying',  'facebook',  'confirmed', 'IELTS tháng 6 mục tiêu 6.5'],
            ['Lê Phương Linh',    '0887654321', 'q1',  'studying',  'referral',  'confirmed', 'Học bổ sung sau giờ học'],
            ['Trần Minh Tuấn',   '0912000111', 'q1',  'studying',  'zalo',      'confirmed', 'Cần học cấp tốc'],
            ['Hoàng Thị Mai',    '0938112233', 'q1',  'studying',  'website',   'confirmed', 'Phụ huynh đặt cọc rồi'],
            ['Võ Gia Bảo',       '0909876543', 'q7',  'studying',  'tiktok',    'confirmed', 'Thích lập trình từ nhỏ'],
            ['Đinh Thùy Dương',  '0970456789', 'q7',  'studying',  'facebook',  'confirmed', 'Python nền tảng đại học'],
            ['Phạm Khánh Linh',  '0965555444', 'q1',  'lead',      'facebook',  'contacted', 'Phụ huynh muốn tư vấn thêm'],
            ['Bùi Gia Huy',      '0334400123', 'q1',  'lead',      'zalo',      'new',       null],
            ['Nguyễn Cẩm Tú',   '0912998877', 'q7',  'lead',      'referral',  'demoed',    'Đã học thử 1 buổi'],
            ['Trương Anh Kiệt',  '0977001002', 'q7',  'dropped',   'website',   null,        'Nghỉ vì bận thi THPT'],
        ];

        $branches = ['q1' => $b1->id, 'q7' => $b2->id];
        $stuObjs   = [];
        foreach ($students as [$name, $phone, $bKey, $status, $src, $lstatus, $notes]) {
            $stuObjs[] = Student::create([
                'tenant_id'   => $tid,
                'branch_id'   => $branches[$bKey],
                'name'        => $name,
                'phone'       => $phone,
                'status'      => $status,
                'lead_source' => $src,
                'lead_status' => $lstatus,
                'notes'       => $notes,
            ]);
        }

        // ══════════════════════════════════════════════════════
        // 7. ENROLLMENTS + SESSIONS + INVOICES
        // ══════════════════════════════════════════════════════
        // Lớp IELTS 6.0 - K12A: 4 học sinh
        $enrollData = [
            [$stuObjs[0], $cls1, 4500000, 4500000], // đầy đủ
            [$stuObjs[1], $cls1, 4500000, 3000000], // còn nợ 1.5tr
            [$stuObjs[2], $cls1, 4200000, 4200000], // giảm giá, đủ
            [$stuObjs[3], $cls1, 4500000, 2000000], // cọc
            // Toán NC K11B: 2 học sinh
            [$stuObjs[1], $cls2, 3200000, 3200000], // học 2 lớp
            [$stuObjs[2], $cls2, 3200000, 1600000],
            // Python Q7A: 2 học sinh
            [$stuObjs[4], $cls3, 2800000, 2800000],
            [$stuObjs[5], $cls3, 2800000, 1400000],
        ];

        $invoiceCount = 1;
        $enrollObjs   = [];

        foreach ($enrollData as [$stu, $cls, $price, $paid]) {
            $enr = Enrollment::create([
                'tenant_id'   => $tid,
                'student_id'  => $stu->id,
                'class_id'    => $cls->id,
                'final_price' => $price,
                'paid_amount' => $paid,
                'status'      => 'active',
            ]);
            $enrollObjs[] = $enr;

            // Create invoice if any payment made
            if ($paid > 0) {
                $code = 'PT-' . now()->format('ym') . '-' . str_pad($invoiceCount++, 3, '0', STR_PAD_LEFT);
                Invoice::create([
                    'tenant_id'        => $tid,
                    'enrollment_id'    => $enr->id,
                    'cashier_id'       => $acct->id,
                    'invoice_code'     => $code,
                    'amount'           => $paid,
                    'payment_method'   => 'cash',
                    'transaction_date' => now()->subDays(rand(1, 20))->toDateString(),
                    'notes'            => 'Học phí kỳ ' . now()->format('m/Y'),
                ]);
            }
        }

        // ══════════════════════════════════════════════════════
        // 8. SESSIONS — generate for Feb + Mar 2026 for active classes
        // ══════════════════════════════════════════════════════
        $this->generateSessions($tid, $cls1, $t1->id, '2026-02-01', '2026-03-31');
        $this->generateSessions($tid, $cls2, $t1->id, '2026-02-01', '2026-03-31');
        $this->generateSessions($tid, $cls3, $t2->id, '2026-03-01', '2026-03-31');

        // Mark some past sessions as completed with attendance
        $this->markCompletedSessions($cls1, $stuObjs);
    }

    private function generateSessions(int $tid, ClassRoom $cls, int $teacherId, string $from, string $to): void
    {
        $rule = $cls->schedule_rule;
        if (empty($rule['days'])) return;

        $dayMap = ['monday'=>1,'tuesday'=>2,'wednesday'=>3,'thursday'=>4,'friday'=>5,'saturday'=>6,'sunday'=>0];
        $targetDays = array_filter(array_map(fn($d) => $dayMap[strtolower($d)] ?? null, $rule['days']));

        $period = \Carbon\CarbonPeriod::create($from, $to);
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

    private function markCompletedSessions(ClassRoom $cls, array $stuObjs): void
    {
        // Grab enrolled students for this class
        $enrolled = $cls->enrollments()->with('student')->get();
        if ($enrolled->isEmpty()) return;

        $completedSessions = ClassSession::where('class_id', $cls->id)
            ->where('status', 'completed')
            ->take(6)->get();

        foreach ($completedSessions as $session) {
            foreach ($enrolled as $enr) {
                $statuses = ['present', 'present', 'present', 'absent_with_leave', 'late'];
                \App\Models\Attendance::firstOrCreate([
                    'session_id' => $session->id,
                    'student_id' => $enr->student_id,
                ], [
                    'tenant_id' => $session->tenant_id,
                    'status'    => $statuses[array_rand($statuses)],
                ]);
            }
        }
    }
}
