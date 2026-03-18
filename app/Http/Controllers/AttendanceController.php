<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    /**
     * Show attendance sheet for a given class session.
     * Also creates empty attendance records for all enrolled students if not yet created.
     */
    public function show(ClassSession $session)
    {
        $session->load(['classroom.enrollments.student']);

        // Auto-create attendance rows for enrolled students if not yet done
        $enrolledStudentIds = $session->classroom->enrollments
            ->where('status', 'active')
            ->pluck('student_id');

        $existingIds = $session->attendances()->pluck('student_id');
        $missing = $enrolledStudentIds->diff($existingIds);

        if ($missing->isNotEmpty()) {
            Attendance::insert(
                $missing->map(fn($sid) => [
                    'tenant_id'  => Auth::user()->tenant_id,
                    'session_id' => $session->id,
                    'student_id' => $sid,
                    'status'     => 'present',
                    'created_at' => now(),
                    'updated_at' => now(),
                ])->all()
            );
        }

        $attendances = $session->attendances()->with('student')->get()
            ->keyBy('student_id');

        return view('attendance.session', compact('session', 'attendances'));
    }

    /**
     * Save attendance for the session (batch update).
     */
    public function store(Request $request, ClassSession $session)
    {
        $request->validate([
            'attendance'          => 'required|array',
            'attendance.*.status' => 'in:present,absent_with_leave,absent_no_leave,late',
        ]);

        foreach ($request->input('attendance', []) as $studentId => $data) {
            Attendance::where('session_id', $session->id)
                ->where('student_id', $studentId)
                ->update([
                    'status'          => $data['status'] ?? 'present',
                    'grade'           => $data['grade'] ?? null,
                    'teacher_comment' => $data['comment'] ?? null,
                ]);
        }

        // Mark session as completed
        $session->update(['status' => 'completed']);

        return back()->with('success', 'Đã lưu điểm danh thành công.');
    }
}
