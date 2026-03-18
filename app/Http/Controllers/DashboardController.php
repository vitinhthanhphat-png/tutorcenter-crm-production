<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke()
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();

        $today = Carbon::today();
        $month = $today->month;
        $year  = $today->year;

        // ─── KPI Cards ───────────────────────────────────────────────
        $totalStudents  = Student::studying()->count();
        $activeClasses  = ClassRoom::active()->count();
        $newLeads       = Student::leads()->count();
        $monthlyRevenue = Invoice::whereMonth('transaction_date', $month)
                                 ->whereYear('transaction_date', $year)
                                 ->sum('amount');

        $stats = compact('totalStudents', 'activeClasses', 'newLeads', 'monthlyRevenue');

        // ─── Today's Schedule ──────────────────────────────────────────
        $todayDow     = strtolower($today->englishDayOfWeek);
        $todayClasses = ClassRoom::active()
            ->with(['teacher', 'course'])
            ->when($user->isTeacher() || $user->isTutor(), fn($q) => $q->where('teacher_id', $user->id))
            ->get()
            ->filter(fn($c) => in_array($todayDow, $c->schedule_rule['days'] ?? []));

        // ─── Recent Leads ─────────────────────────────────────────────
        $recentLeads = Student::leads()->latest()->limit(5)->get();

        // ─── Attendance Rate (this month) ──────────────────────────────
        $attendanceStats = Attendance::join('class_sessions', 'attendances.class_session_id', '=', 'class_sessions.id')
            ->whereMonth('class_sessions.date', $month)
            ->whereYear('class_sessions.date', $year)
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN attendances.status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN attendances.status = 'absent' THEN 1 ELSE 0 END) as absent
            ")
            ->first();

        $attendanceRate = $attendanceStats->total > 0
            ? round(($attendanceStats->present / $attendanceStats->total) * 100, 1)
            : 0;

        // ─── Dropout / Stopped Students ──────────────────────────────
        $droppedThisMonth = Student::where('status', 'inactive')
            ->whereMonth('updated_at', $month)
            ->whereYear('updated_at', $year)
            ->count();

        // ─── Revenue Trend (last 6 months) ───────────────────────────
        $revenueTrend = Invoice::selectRaw("
                DATE_FORMAT(transaction_date, '%Y-%m') as month,
                SUM(amount) as total
            ")
            ->where('transaction_date', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_FORMAT(transaction_date, '%Y-%m')")
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ─── Student Growth (last 6 months) ──────────────────────────
        $studentGrowth = Student::selectRaw("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as total
            ")
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->groupByRaw("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // ─── Unpaid Invoices (overdue) ────────────────────────────────
        $overdueInvoices = Invoice::where('status', 'pending')
            ->where('due_date', '<', $today)
            ->with(['student'])
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats', 'todayClasses', 'recentLeads',
            'attendanceRate', 'droppedThisMonth', 'attendanceStats',
            'revenueTrend', 'studentGrowth', 'overdueInvoices',
        ));
    }
}
