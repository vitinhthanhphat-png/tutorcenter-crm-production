<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\ClassSession;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * StudentPortalController
 *
 * Self-service portal for students and parents.
 * Route prefix: /portal
 * Middleware: auth, role:student,parent
 */
class StudentPortalController extends Controller
{
    /** Portal homepage — TKB + recent attendance */
    public function index()
    {
        $user    = Auth::user();
        $student = $this->resolveStudent($user);

        if (! $student) {
            return view('portal.no-student');
        }

        // Current active enrollments
        $enrollments = $student->enrollments()
            ->with(['classroom.course', 'classroom.teacher'])
            ->where('status', 'active')
            ->get();

        // Upcoming sessions for enrolled classes (next 14 days)
        $classIds = $enrollments->pluck('class_id');
        $upcoming = ClassSession::whereIn('class_id', $classIds)
            ->where('date', '>=', now()->toDateString())
            ->where('date', '<=', now()->addDays(14)->toDateString())
            ->with(['classroom.course'])
            ->orderBy('date')
            ->orderBy('start_time')
            ->limit(10)
            ->get();

        // Recent attendance (last 30 days)
        $recentAttendance = Attendance::where('student_id', $student->id)
            ->with(['session.classroom.course'])
            ->whereHas('session', fn($q) => $q->where('date', '>=', now()->subDays(30)->toDateString()))
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        // Unpaid invoices
        $unpaidInvoices = Invoice::where('student_id', $student->id)
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();

        return view('portal.index', compact(
            'student', 'enrollments', 'upcoming', 'recentAttendance', 'unpaidInvoices'
        ));
    }

    /** Full attendance history for the student */
    public function attendance(Request $request)
    {
        $user    = Auth::user();
        $student = $this->resolveStudent($user);

        if (! $student) {
            return view('portal.no-student');
        }

        $query = Attendance::where('student_id', $student->id)
            ->with(['session.classroom.course'])
            ->orderByDesc('id');

        if ($request->filled('month')) {
            $query->whereHas('session', fn($q) =>
                $q->whereRaw("DATE_FORMAT(date,'%Y-%m') = ?", [$request->month])
            );
        }

        $records = $query->paginate(20)->appends(request()->query());

        // Summary counts
        $summary = Attendance::where('student_id', $student->id)
            ->selectRaw("status, COUNT(*) as cnt")
            ->groupBy('status')
            ->pluck('cnt', 'status');

        return view('portal.attendance', compact('student', 'records', 'summary'));
    }

    /** Invoice / payment history */
    public function invoices()
    {
        $user    = Auth::user();
        $student = $this->resolveStudent($user);

        if (! $student) {
            return view('portal.no-student');
        }

        $invoices = Invoice::where('student_id', $student->id)
            ->orderByDesc('transaction_date')
            ->paginate(20);

        $totalPaid    = Invoice::where('student_id', $student->id)->where('status', 'paid')->sum('amount');
        $totalPending = Invoice::where('student_id', $student->id)->where('status', 'pending')->sum('amount');

        return view('portal.invoices', compact('student', 'invoices', 'totalPaid', 'totalPending'));
    }

    /**
     * Resolve the Student record from the logged-in user.
     * Students log in with a User account linked via student.user_id.
     * Parents can view their child via student.parent_id (if ParentProfile linked).
     */
    private function resolveStudent($user): ?Student
    {
        // Direct student login: user linked to student record
        return Student::where('user_id', $user->id)->with(['tenant', 'branch'])->first()
            ?? Student::where('parent_id', $user->id)->with(['tenant', 'branch'])->first();
    }
}
