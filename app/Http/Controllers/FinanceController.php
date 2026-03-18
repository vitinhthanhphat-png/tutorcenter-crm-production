<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function index()
    {
        return redirect()->route('finance.invoices');
    }

    public function invoices()
    {
        $invoices = Invoice::with(['enrollment.student', 'enrollment.classroom', 'cashier'])
            ->latest('transaction_date')
            ->paginate(25);

        $monthlySummary = [
            'income'    => Invoice::whereMonth('transaction_date', now()->month)->sum('amount'),
            'total_debt' => Enrollment::sum(\Illuminate\Support\Facades\DB::raw('final_price - paid_amount')),
            'invoice_count' => Invoice::whereMonth('transaction_date', now()->month)->count(),
        ];

        return view('finance.invoices', compact('invoices', 'monthlySummary'));
    }

    public function storeInvoice(Request $request)
    {
        $validated = $request->validate([
            'enrollment_id'    => 'required|exists:enrollments,id',
            'amount'           => 'required|numeric|min:1',
            'payment_method'   => 'in:cash,transfer,card,other',
            'transaction_date' => 'required|date',
            'notes'            => 'nullable|string|max:500',
        ]);

        $invoice = Invoice::create([
            ...$validated,
            'cashier_id'   => auth()->id(),
            'invoice_code' => 'PT-' . now()->format('ym') . '-' . str_pad(Invoice::whereMonth('transaction_date', now()->month)->count() + 1, 3, '0', STR_PAD_LEFT),
        ]);

        return back()->with('success', "Đã ghi nhận phiếu thu {$invoice->invoice_code} thành công.");
    }

    /**
     * Monthly revenue report with Chart.js data.
     */
    public function report()
    {
        // Last 12 months revenue
        $months = collect(range(11, 0))->map(function ($i) {
            $date = now()->subMonths($i);
            $revenue = Invoice::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            return [
                'label'   => $date->format('m/Y'),
                'revenue' => (int) $revenue,
            ];
        });

        $stats = [
            'total_revenue_ytd' => Invoice::whereYear('transaction_date', now()->year)->sum('amount'),
            'total_this_month'  => Invoice::whereMonth('transaction_date', now()->month)
                                          ->whereYear('transaction_date', now()->year)->sum('amount'),
            'total_debt'        => \App\Models\Enrollment::selectRaw('SUM(final_price - paid_amount) as debt')
                                          ->value('debt') ?? 0,
            'active_students'   => \App\Models\Enrollment::where('status', 'active')->count(),
        ];

        return view('finance.report', [
            'months'       => $months,
            'chartLabels'  => $months->pluck('label')->toJson(),
            'chartRevenue' => $months->pluck('revenue')->toJson(),
            'stats'        => $stats,
        ]);
    }
}

