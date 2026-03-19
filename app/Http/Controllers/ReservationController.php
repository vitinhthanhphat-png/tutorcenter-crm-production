<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    /**
     * Show reservation form for an enrollment.
     */
    public function create(Enrollment $enrollment)
    {
        $enrollment->load(['student', 'classroom']);

        return view('reservation.create', compact('enrollment'));
    }

    /**
     * Store a new reservation (pause) on the enrollment.
     */
    public function store(Request $request, Enrollment $enrollment)
    {
        $request->validate([
            'reserved_at'        => 'required|date',
            'reservation_ends_at'=> 'required|date|after:reserved_at',
            'reservation_note'   => 'nullable|string|max:1000',
        ]);

        $enrollment->update([
            'status'             => 'reserved',
            'reserved_at'        => $request->reserved_at,
            'reservation_ends_at'=> $request->reservation_ends_at,
            'reservation_note'   => $request->reservation_note,
        ]);

        // Update student status to reserved if they have no other active enrollment
        $student = $enrollment->student;
        $hasActiveEnrollment = $student->enrollments()
            ->where('id', '!=', $enrollment->id)
            ->where('status', 'active')
            ->exists();

        if (!$hasActiveEnrollment) {
            $student->update(['status' => 'reserved']);
        }

        return redirect()->route('students.show', $enrollment->student_id)
            ->with('success', 'Đã bảo lưu khóa học thành công.');
    }

    /**
     * Reactivate a reserved enrollment.
     */
    public function reactivate(Enrollment $enrollment)
    {
        $enrollment->update([
            'status'             => 'active',
            'reserved_at'        => null,
            'reservation_ends_at'=> null,
            'reservation_note'   => null,
        ]);

        // Reactivate student status
        $enrollment->student->update(['status' => 'studying']);

        return redirect()->route('students.show', $enrollment->student_id)
            ->with('success', 'Đã kích hoạt lại khóa học thành công.');
    }
}
