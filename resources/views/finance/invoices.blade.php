<x-app-layout>
    <x-slot name="heading">Báo cáo Tài chính</x-slot>
    <x-slot name="subheading">Phiếu thu & Công nợ học sinh</x-slot>
    <x-slot name="actions">
        <button onclick="document.getElementById('createInvoiceModal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
            Tạo phiếu thu
        </button>
        <a href="#" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Xuất Excel
        </a>
    </x-slot>

    <div class="p-8 space-y-6">

        @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-3 gap-5">
            <div class="bg-white border border-gray-100 p-5">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Doanh thu tháng này</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" style="font-family:'Space Grotesk',sans-serif">
                    {{ number_format($monthlySummary['income']) }}<span class="text-sm font-normal text-gray-400 ml-1">đ</span>
                </p>
            </div>
            <div class="bg-white border border-gray-100 p-5">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Công nợ còn lại</p>
                <p class="text-2xl font-bold text-red-600 mt-1" style="font-family:'Space Grotesk',sans-serif">
                    {{ number_format(max(0, $monthlySummary['total_debt'])) }}<span class="text-sm font-normal text-gray-400 ml-1">đ</span>
                </p>
            </div>
            <div class="bg-white border border-gray-100 p-5">
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Số phiếu thu tháng này</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" style="font-family:'Space Grotesk',sans-serif">{{ $monthlySummary['invoice_count'] }}</p>
            </div>
        </div>

        {{-- Invoices Table --}}
        <div class="bg-white border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Mã phiếu</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Học sinh</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Lớp học</th>
                        <th class="px-5 py-3 text-right text-xs font-medium text-gray-400 uppercase tracking-wide">Số tiền</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Ngày</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Hình thức</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Thu ngân</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $invoice)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-sm font-mono text-gray-600">{{ $invoice->invoice_code ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $invoice->enrollment?->student?->name ?? '—' }}</p>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $invoice->enrollment?->classroom?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-sm font-semibold text-gray-900 text-right">{{ number_format($invoice->amount) }}đ</td>
                        <td class="px-5 py-3 text-sm text-gray-500">{{ $invoice->transaction_date->format('d/m/Y') }}</td>
                        <td class="px-5 py-3">
                            @php $payMap = ['cash'=>'Tiền mặt','transfer'=>'Chuyển khoản','card'=>'Thẻ','other'=>'Khác']; @endphp
                            <span class="text-xs px-2 py-0.5 bg-blue-50 text-blue-700">{{ $payMap[$invoice->payment_method] ?? '—' }}</span>
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-400">{{ $invoice->cashier?->name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">Chưa có phiếu thu nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $invoices->links() }}</div>

    </div>
</x-app-layout>
