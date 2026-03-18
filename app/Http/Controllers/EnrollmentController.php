<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    /**
     * Store a new enrollment (student → class link).
     * Triggered via the modal in classes/show.blade.php.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id'  => 'required|exists:students,id',
            'class_id'    => 'required|exists:classes,id',
            'final_price' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0|lte:final_price',
            'start_date'  => 'required|date',
        ]);

        // Prevent duplicate enrollment
        $existing = Enrollment::where('student_id', $validated['student_id'])
            ->where('class_id', $validated['class_id'])
            ->first();

        if ($existing) {
            return back()->withErrors(['student_id' => 'Học sinh này đã được ghi danh vào lớp.']);
        }

        $enrollment = Enrollment::create([
            ...$validated,
            'status' => 'active',
        ]);

        // If there's an initial payment, auto-create an invoice
        if ($validated['paid_amount'] > 0) {
            $enrollment->invoices()->create([
                'tenant_id'        => auth()->user()->tenant_id,
                'cashier_id'       => auth()->id(),
                'amount'           => $validated['paid_amount'],
                'payment_method'   => 'cash',
                'transaction_date' => now()->toDateString(),
                'invoice_code'     => 'PT-' . now()->format('ym') . '-' . str_pad(
                    \App\Models\Invoice::whereMonth('transaction_date', now()->month)->count() + 1,
                    3, '0', STR_PAD_LEFT
                ),
                'notes'            => 'Đặt cọc khi ghi danh',
            ]);

            // Update enrollment paid_amount
            $enrollment->increment('paid_amount', 0); // already set in create
        }

        // Update student status to 'studying' if they were a lead
        $student = Student::find($validated['student_id']);
        if ($student && $student->status === 'lead') {
            $student->update(['status' => 'studying']);
        }

        return redirect()->route('classes.show', $validated['class_id'])
            ->with('success', "Đã ghi danh {$student->name} vào lớp thành công.");
    }
}
