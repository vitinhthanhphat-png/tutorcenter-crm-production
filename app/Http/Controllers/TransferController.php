<?php

namespace App\Http\Controllers;

use App\Models\ClassRoom;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    /** Show transfer form for a given enrollment */
    public function create(Enrollment $enrollment)
    {
        abort_if(!in_array($enrollment->status, ['active', 'reserved']), 403, 'Chỉ enrollment đang học mới có thể chuyển lớp.');

        // Available target classes (same tenant, not the current class, accepting students)
        $classes = ClassRoom::where('id', '!=', $enrollment->class_id)
            ->active()
            ->orderBy('name')
            ->get();

        return view('transfer.create', compact('enrollment', 'classes'));
    }

    /** Execute the transfer */
    public function store(Request $request, Enrollment $enrollment)
    {
        $data = $request->validate([
            'target_class_id' => 'required|exists:class_rooms,id|different:' . $enrollment->class_id,
            'transfer_note'   => 'nullable|string|max:300',
        ]);

        abort_if(!in_array($enrollment->status, ['active', 'reserved']), 403);

        DB::transaction(function () use ($enrollment, $data) {
            $paidAmount    = $enrollment->paid_amount;
            $remainBalance = $enrollment->final_price > 0
                ? $paidAmount - $enrollment->final_price
                : 0;
            $creditBalance = max(0, $remainBalance);

            // 1. Create new enrollment in the target class
            $newEnrollment = Enrollment::create([
                'tenant_id'           => $enrollment->tenant_id,
                'student_id'          => $enrollment->student_id,
                'class_id'            => $data['target_class_id'],
                'final_price'         => $enrollment->final_price,
                'paid_amount'         => $creditBalance,  // carry-over credit
                'discount_note'       => 'Chuyển lớp từ ' . $enrollment->classroom?->name,
                'status'              => 'active',
                'enrolled_by'         => Auth::id(),
                'notes'               => $data['transfer_note'] ?? null,
                'transferred_from_id' => $enrollment->id,
                'credit_balance'      => $creditBalance,
            ]);

            // 2. Mark original enrollment as transferred
            $enrollment->update([
                'status'            => 'transferred',
                'transferred_to_id' => $newEnrollment->id,
                'transferred_at'    => now(),
                'transfer_note'     => $data['transfer_note'] ?? null,
            ]);

            // 3. If there is leftover credit, create a credit invoice
            if ($creditBalance > 0) {
                Invoice::create([
                    'tenant_id'        => $enrollment->tenant_id,
                    'student_id'       => $enrollment->student_id,
                    'enrollment_id'    => $newEnrollment->id,
                    'amount'           => -$creditBalance,  // negative = credit
                    'description'      => "Số dư chuyển từ lớp {$enrollment->classroom?->name}",
                    'transaction_date' => now()->toDateString(),
                    'recorded_by'      => Auth::id(),
                ]);
            }
        });

        return redirect()
            ->route('students.show', $enrollment->student_id)
            ->with('success', '✅ Đã chuyển lớp thành công và ghi nhận số dư.');
    }
}
