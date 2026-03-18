<?php

namespace App\Http\Controllers;

use App\Models\ClassSession;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

        $payrolls = Payroll::with(['teacher', 'branch'])
            ->where('month', $month)
            ->orderBy('status')
            ->paginate(20)->appends(request()->query());


        $teachers = User::whereIn('role', ['teacher', 'tutor'])->orderBy('name')->get();

        return view('payroll.index', compact('payrolls', 'month', 'teachers'));
    }

    /** Auto-generate payroll from session attendance data */
    public function generate(Request $request)
    {
        $data = $request->validate([
            'month'            => 'required|date_format:Y-m',
            'user_id'          => 'required|exists:users,id',
            'rate_per_session' => 'nullable|integer|min:0',
            'rate_per_hour'    => 'nullable|integer|min:0',
            'base_salary'      => 'nullable|integer|min:0',
            'bonus'            => 'nullable|integer|min:0',
            'deduction'        => 'nullable|integer|min:0',
            'note'             => 'nullable|string',
        ]);

        $teacher = User::findOrFail($data['user_id']);

        // Count sessions taught in the month
        [$year, $month] = explode('-', $data['month']);
        $sessions = ClassSession::where('teacher_id', $teacher->id)
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        $totalSessions = $sessions->count();
        $totalHours    = $sessions->sum('duration_minutes') / 60;

        $ratePerSession = (int)($data['rate_per_session'] ?? 0);
        $ratePerHour    = (int)($data['rate_per_hour'] ?? 0);
        $baseSalary     = (int)($data['base_salary'] ?? 0);
        $bonus          = (int)($data['bonus'] ?? 0);
        $deduction      = (int)($data['deduction'] ?? 0);

        $sessionPay = ($totalSessions * $ratePerSession) + ((int)$totalHours * $ratePerHour);
        $total      = $baseSalary + $sessionPay + $bonus - $deduction;

        $payroll = Payroll::updateOrCreate(
            ['user_id' => $teacher->id, 'month' => $data['month'], 'tenant_id' => Auth::user()->tenant_id],
            [
                'branch_id'        => $teacher->branch_id,
                'total_sessions'   => $totalSessions,
                'total_hours'      => (int)$totalHours,
                'rate_per_session' => $ratePerSession,
                'rate_per_hour'    => $ratePerHour,
                'base_salary'      => $baseSalary,
                'session_pay'      => $sessionPay,
                'bonus'            => $bonus,
                'deduction'        => $deduction,
                'total'            => max(0, $total),
                'status'           => 'draft',
                'note'             => $data['note'] ?? null,
                'created_by'       => Auth::id(),
            ]
        );

        return redirect()->route('payroll.index', ['month' => $data['month']])
            ->with('success', "📝 Đã tạo phiếu lương cho {$teacher->name} — tháng {$data['month']}.");
    }

    public function confirm(Payroll $payroll)
    {
        abort_if($payroll->status !== 'draft', 400, 'Chỉ có thể xác nhận phiếu nháp.');
        $payroll->update(['status' => 'confirmed']);
        return back()->with('success', '✅ Đã xác nhận phiếu lương.');
    }

    public function markPaid(Payroll $payroll)
    {
        abort_if($payroll->status !== 'confirmed', 400, 'Phiếu phải được xác nhận trước.');
        $payroll->update(['status' => 'paid']);
        return back()->with('success', '💰 Đã đánh dấu đã thanh toán.');
    }

    public function destroy(Payroll $payroll)
    {
        abort_if($payroll->status !== 'draft', 400, 'Chỉ có thể xóa phiếu nháp.');
        $payroll->delete();
        return back()->with('success', 'Đã xóa phiếu lương.');
    }
}
