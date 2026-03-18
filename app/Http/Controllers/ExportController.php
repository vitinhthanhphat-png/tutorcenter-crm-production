<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use App\Models\ClassSession;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    /**
     * Export hub — landing page with all export options
     */
    public function index()
    {
        return view('export.index');
    }

    /**
     * Export danh sách học sinh (CSV)
     */
    public function students(Request $request)
    {
        $data = $request->validate([
            'status' => 'nullable|in:studying,lead,reserved,inactive',
        ]);

        $students = Student::with(['branch'])
            ->when($data['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->orderBy('name')
            ->get();

        $filename = 'hoc-sinh-' . now()->format('Ymd') . '.csv';
        return response()->streamDownload(function () use ($students) {
            $handle = fopen('php://output', 'w');
            // BOM for Excel UTF-8
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['STT', 'Họ tên', 'SĐT', 'Email', 'Chi nhánh', 'Trạng thái', 'Ngày tạo']);
            foreach ($students as $i => $s) {
                fputcsv($handle, [
                    $i + 1,
                    $s->name,
                    $s->phone ?? '',
                    $s->email ?? '',
                    $s->branch?->name ?? '',
                    $s->status,
                    $s->created_at?->format('d/m/Y'),
                ]);
            }
            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Export Sổ thu chi (CSV)
     */
    public function cashbook(Request $request)
    {
        $data = $request->validate([
            'month' => 'nullable|date_format:Y-m',
            'type'  => 'nullable|in:income,expense',
        ]);

        $month = $data['month'] ?? now()->format('Y-m');

        $entries = Cashbook::with(['branch', 'recorder'])
            ->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$month])
            ->when($data['type'] ?? null, fn($q, $t) => $q->where('type', $t))
            ->orderBy('transaction_date')
            ->get();

        $filename = "so-thu-chi-{$month}.csv";
        return response()->streamDownload(function () use ($entries) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF");
            fputcsv($h, ['Ngày', 'Loại', 'Danh mục', 'Mô tả', 'Số tiền', 'Tham chiếu', 'Người ghi']);
            foreach ($entries as $e) {
                fputcsv($h, [
                    $e->transaction_date->format('d/m/Y'),
                    $e->type === 'income' ? 'Thu' : 'Chi',
                    $e->category,
                    $e->description,
                    $e->amount,
                    $e->reference ?? '',
                    $e->recorder?->name ?? '',
                ]);
            }
            fclose($h);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Export Bảng lương (CSV)
     */
    public function payroll(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

        $records = \App\Models\Payroll::with(['teacher', 'branch'])
            ->where('month', $month)
            ->orderBy('status')
            ->get();

        $filename = "bang-luong-{$month}.csv";
        return response()->streamDownload(function () use ($records) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF");
            fputcsv($h, ['Giáo viên', 'Tháng', 'Số buổi', 'Lương buổi', 'Lương cứng', 'Thưởng', 'Khấu trừ', 'Tổng cộng', 'Trạng thái']);
            foreach ($records as $r) {
                fputcsv($h, [
                    $r->teacher?->name,
                    $r->month,
                    $r->total_sessions,
                    $r->session_pay,
                    $r->base_salary,
                    $r->bonus,
                    $r->deduction,
                    $r->total,
                    $r->statusLabel(),
                ]);
            }
            fclose($h);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Export Danh sách học viên & số giờ trong tháng (CSV)
     */
    public function attendance(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));
        [$year, $mon] = explode('-', $month);

        $sessions = ClassSession::with(['classroom', 'teacher', 'attendances.student'])
            ->whereYear('date', $year)
            ->whereMonth('date', $mon)
            ->get();

        $filename = "diem-danh-{$month}.csv";
        return response()->streamDownload(function () use ($sessions) {
            $h = fopen('php://output', 'w');
            fwrite($h, "\xEF\xBB\xBF");
            fputcsv($h, ['Ngày', 'Lớp', 'Giáo viên', 'Học sinh', 'Trạng thái điểm danh']);
            foreach ($sessions as $s) {
                foreach ($s->attendances as $att) {
                    fputcsv($h, [
                        $s->date->format('d/m/Y'),
                        $s->classroom?->name ?? '',
                        $s->teacher?->name ?? '',
                        $att->student?->name ?? '',
                        $att->status,
                    ]);
                }
            }
            fclose($h);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
