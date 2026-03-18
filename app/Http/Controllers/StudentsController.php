<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentsController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['branch']);

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $students = $query->latest()->paginate(25);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        return view('students.form');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'phone'       => 'nullable|string|max:20',
            'dob'         => 'nullable|date',
            'school'      => 'nullable|string|max:200',
            'status'      => 'in:lead,studying,dropped,graduated,reserved',
            'lead_source' => 'nullable|string|max:100',
            'lead_status' => 'nullable|string|max:100',
            'notes'       => 'nullable|string|max:1000',
        ]);

        Student::create($validated);

        return redirect()->route('students.index')
            ->with('success', 'Đã thêm học sinh / lead thành công.');
    }

    public function show(Student $student)
    {
        $student->load(['enrollments.classroom', 'enrollments.invoices']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        return view('students.form', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:150',
            'phone'       => 'nullable|string|max:20',
            'status'      => 'in:lead,studying,dropped,graduated,reserved',
            'lead_source' => 'nullable|string|max:100',
            'lead_status' => 'nullable|string|max:100',
            'notes'       => 'nullable|string|max:1000',
        ]);

        $student->update($validated);

        return redirect()->route('students.index')
            ->with('success', 'Đã cập nhật thông tin học sinh.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Đã xóa học sinh.');
    }
}
