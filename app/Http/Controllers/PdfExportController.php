<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Invoice;
use App\Models\Payroll;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PdfExportController extends Controller
{
    /**
     * Export student profile as PDF.
     */
    public function studentPdf(Student $student)
    {
        $student->load(['branch', 'enrollments.classroom', 'enrollments.invoices']);

        $pdf = Pdf::loadView('pdf.student-profile', compact('student'))
            ->setPaper('a4', 'portrait');

        $filename = 'HS_' . str_replace(' ', '_', $student->name) . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export invoice as PDF.
     */
    public function invoicePdf(Invoice $invoice)
    {
        $invoice->load(['enrollment.student', 'enrollment.classroom', 'cashier']);

        $tenant = Auth::user()->tenant;

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'tenant'))
            ->setPaper('a5', 'portrait');

        $filename = 'PT_' . ($invoice->invoice_code ?? $invoice->id) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export monthly payroll as PDF.
     */
    public function payrollPdf(Request $request)
    {
        $month = $request->get('month', now()->format('Y-m'));

        $payrolls = Payroll::with(['teacher'])
            ->where('month', $month)
            ->where('tenant_id', Auth::user()->tenant_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $tenant = Auth::user()->tenant;

        $pdf = Pdf::loadView('pdf.payroll', compact('payrolls', 'month', 'tenant'))
            ->setPaper('a4', 'landscape');

        $filename = 'BangLuong_' . str_replace('-', '_', $month) . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Export attendance report for a class as PDF.
     */
    public function attendancePdf(ClassRoom $classroom)
    {
        $classroom->load([
            'sessions' => fn($q) => $q->orderBy('date')->with('attendances.student'),
            'enrollments.student',
        ]);

        $tenant = Auth::user()->tenant;

        $pdf = Pdf::loadView('pdf.attendance', compact('classroom', 'tenant'))
            ->setPaper('a4', 'landscape');

        $filename = 'DiemDanh_' . str_replace(' ', '_', $classroom->name) . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
