<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\ClassRoom;
use App\Models\Student;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    /**
     * Grades landing page — list all classes to pick a grade report.
     */
    public function list()
    {
        $user = auth()->user();
        $classes = ClassRoom::where('tenant_id', $user->tenant_id)
            ->where('status', '!=', 'planned')
            ->with(['course', 'teacher', 'branch'])
            ->orderBy('name')
            ->get();

        return view('grades.index', compact('classes'));
    }

    /**
     * Show grade matrix for a class (sessions × students).
     */
    public function index(ClassRoom $classroom)
    {
        $classroom->load([
            'sessions' => fn($q) => $q->orderBy('date'),
            'enrollments.student',
        ]);

        // Build a map: student_id → [session_id → attendance]
        $sessions = $classroom->sessions;
        $enrollments = $classroom->enrollments->where('status', 'active');
        $studentIds = $enrollments->pluck('student_id');

        $attendances = Attendance::whereIn('session_id', $sessions->pluck('id'))
            ->whereIn('student_id', $studentIds)
            ->get()
            ->groupBy('student_id');

        return view('grades.class-report', compact('classroom', 'sessions', 'enrollments', 'attendances'));
    }

    /**
     * Show grade detail for a single student across all their classes.
     */
    public function show(Student $student)
    {
        $student->load([
            'enrollments.classroom.sessions' => fn($q) => $q->orderBy('date'),
        ]);

        // Collect all attendances for this student across all enrolled classes
        $enrollmentIds = $student->enrollments->pluck('id');
        $sessionIds = $student->enrollments->flatMap(fn($e) => $e->classroom?->sessions ?? collect())->pluck('id');

        $attendances = Attendance::where('student_id', $student->id)
            ->whereIn('session_id', $sessionIds)
            ->get()
            ->keyBy('session_id');

        return view('grades.student-report', compact('student', 'attendances'));
    }
}
