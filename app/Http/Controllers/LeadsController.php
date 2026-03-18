<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Lead;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadsController extends Controller
{
    public function index(Request $request)
    {
        $query = Lead::with(['branch', 'assignedTo'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->q}%")
                  ->orWhere('phone', 'like', "%{$request->q}%")
                  ->orWhere('email', 'like', "%{$request->q}%");
            });
        }

        $leads    = $query->paginate(20)->appends(request()->query());

        $statuses = Lead::statuses();
        $summary  = Lead::selectRaw('status, count(*) as count')->groupBy('status')->pluck('count', 'status');

        return view('leads.index', compact('leads', 'statuses', 'summary'));
    }

    public function create()
    {
        $branches = Branch::orderBy('name')->get();
        $staff    = User::whereIn('role', ['center_manager', 'branch_manager', 'operations'])->orderBy('name')->get();
        $statuses = Lead::statuses();
        return view('leads.form', ['lead' => null, 'branches' => $branches, 'staff' => $staff, 'statuses' => $statuses]);
    }

    public function store(Request $request)
    {
        $data = $this->validateLead($request);
        $data['assigned_to'] = Auth::id();
        Lead::create($data);
        return redirect()->route('leads.index')->with('success', 'Đã thêm lead mới.');
    }

    public function show(Lead $lead)
    {
        $lead->load(['branch', 'assignedTo', 'student']);
        return view('leads.show', compact('lead'));
    }

    public function edit(Lead $lead)
    {
        $branches = Branch::orderBy('name')->get();
        $staff    = User::whereIn('role', ['center_manager', 'branch_manager', 'operations'])->orderBy('name')->get();
        $statuses = Lead::statuses();
        return view('leads.form', compact('lead', 'branches', 'staff', 'statuses'));
    }

    public function update(Request $request, Lead $lead)
    {
        $lead->update($this->validateLead($request, $lead->id));
        return redirect()->route('leads.index')->with('success', 'Đã cập nhật lead.');
    }

    public function destroy(Lead $lead)
    {
        $lead->delete();
        return back()->with('success', 'Đã xóa lead.');
    }

    /** Convert lead → student */
    public function convert(Lead $lead)
    {
        if ($lead->converted_to_student_id) {
            return back()->with('error', 'Lead này đã được chuyển đổi thành học sinh.');
        }

        $student = Student::create([
            'tenant_id'  => $lead->tenant_id,
            'branch_id'  => $lead->branch_id,
            'name'       => $lead->name,
            'phone'      => $lead->phone,
            'email'      => $lead->email,
        ]);

        $lead->update([
            'status'                  => 'registered',
            'converted_to_student_id' => $student->id,
            'converted_at'            => now(),
        ]);

        return redirect()->route('students.show', $student)->with('success', "✅ Đã tạo học sinh từ lead {$lead->name}.");
    }

    private function validateLead(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name'              => 'required|string|max:100',
            'phone'             => 'nullable|string|max:20',
            'email'             => 'nullable|email|max:100',
            'parent_name'       => 'nullable|string|max:100',
            'status'            => 'required|in:new,contacted,consulting,test_booked,registered,lost',
            'source'            => 'nullable|string|max:50',
            'interested_course' => 'nullable|string|max:100',
            'note'              => 'nullable|string',
            'assigned_to'       => 'nullable|exists:users,id',
            'follow_up_at'      => 'nullable|date',
            'branch_id'         => 'nullable|exists:branches,id',
        ]);
    }
}
