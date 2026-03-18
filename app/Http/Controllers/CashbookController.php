<?php

namespace App\Http\Controllers;

use App\Models\Cashbook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashbookController extends Controller
{
    public function index(Request $request)
    {
        $query = Cashbook::with(['branch', 'recorder'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('id');

        if ($request->filled('type'))  $query->where('type', $request->type);
        if ($request->filled('month')) $query->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$request->month]);

        $entries = $query->paginate(25)->appends(request()->query());


        // Totals (scoped to filters)
        $income  = Cashbook::where('type', 'income')
            ->when($request->filled('month'), fn($q) => $q->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$request->month]))
            ->sum('amount');
        $expense = Cashbook::where('type', 'expense')
            ->when($request->filled('month'), fn($q) => $q->whereRaw("DATE_FORMAT(transaction_date, '%Y-%m') = ?", [$request->month]))
            ->sum('amount');

        $categories = Cashbook::categories();

        return view('cashbook.index', compact('entries', 'income', 'expense', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'type'             => 'required|in:income,expense',
            'category'         => 'required|string|max:80',
            'description'      => 'required|string|max:200',
            'amount'           => 'required|integer|min:1',
            'transaction_date' => 'required|date',
            'reference'        => 'nullable|string|max:50',
            'note'             => 'nullable|string|max:500',
        ]);
        $data['recorded_by'] = Auth::id();
        Cashbook::create($data);
        return back()->with('success', 'Đã ghi nhận giao dịch.');
    }

    public function destroy(Cashbook $cashbook)
    {
        $cashbook->delete();
        return back()->with('success', 'Đã xóa giao dịch.');
    }
}
