<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\ClassRoom;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    public function index()
    {
        $query = ClassRoom::with(['course', 'teacher', 'branch'])->latest();

        // Teachers only see their own classes
        if (auth()->user()->role === 'teacher') {
            $query->where('teacher_id', auth()->id());
        }

        // Status filter from query string
        if (request('status')) {
            $query->where('status', request('status'));
        }

        $classes = $query->paginate(20);

        return view('classes.index', compact('classes'));
    }

    public function create()
    {
        $courses  = Course::where('is_active', true)->get();
        $teachers = User::whereIn('role', ['teacher'])->get();
        $branches = Branch::where('is_active', true)->get();

        return view('classes.form', compact('courses', 'teachers', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'course_id'  => 'required|exists:courses,id',
            'branch_id'  => 'required|exists:branches,id',
            'teacher_id' => 'nullable|exists:users,id',
            'room_name'  => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'max_students' => 'integer|min:1|max:100',
            'schedule_rule' => 'nullable|array',
            'status'     => 'in:planned,active,completed,cancelled',
        ]);

        ClassRoom::create($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Lớp học đã được tạo thành công.');
    }

    public function show(ClassRoom $class)
    {
        $class->load(['course', 'teacher', 'tutor', 'branch', 'sessions' => fn($q) => $q->latest()->limit(10)]);
        $enrollments = $class->enrollments()->with('student')->get();

        return view('classes.show', compact('class', 'enrollments'));
    }

    public function edit(ClassRoom $class)
    {
        $courses  = Course::where('is_active', true)->get();
        $teachers = User::whereIn('role', ['teacher'])->get();
        $branches = Branch::where('is_active', true)->get();

        return view('classes.form', compact('class', 'courses', 'teachers', 'branches'));
    }

    public function update(Request $request, ClassRoom $class)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:150',
            'course_id'  => 'required|exists:courses,id',
            'teacher_id' => 'nullable|exists:users,id',
            'room_name'  => 'nullable|string|max:50',
            'status'     => 'in:planned,active,completed,cancelled',
        ]);

        $class->update($validated);

        return redirect()->route('classes.index')
            ->with('success', 'Lớp học đã được cập nhật.');
    }

    public function destroy(ClassRoom $class)
    {
        $class->delete();

        return redirect()->route('classes.index')
            ->with('success', 'Đã xóa lớp học.');
    }
}
