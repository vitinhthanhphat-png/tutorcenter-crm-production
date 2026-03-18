<x-app-layout>
    <x-slot name="heading">Lịch sử Học phí</x-slot>
    <x-slot name="subheading">Các hóa đơn học phí của {{ $student->name }}</x-slot>

    <div class="p-6 space-y-4">

        {{-- KPI summary --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="bg-white border border-gray-100 p-4">
                <p class="text-xs text-gray-400">Tổng đã thanh toán</p>
                <p class="text-xl font-bold text-green-600 mt-1">{{ number_format($totalPaid) }}đ</p>
            </div>
            <div class="bg-white border border-gray-100 p-4">
                <p class="text-xs text-gray-400">Chưa thanh toán</p>
                <p class="text-xl font-bold text-red-500 mt-1">{{ number_format($totalPending) }}đ</p>
            </div>
        </div>

        {{-- Invoice table --}}
        <div class="bg-white border border-gray-100">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-400 font-medium">
                        <th class="px-5 py-3 text-left">Ngày</th>
                        <th class="px-5 py-3 text-left">Mô tả</th>
                        <th class="px-5 py-3 text-right">Số tiền</th>
                        <th class="px-5 py-3 text-center">Trạng thái</th>
                        <th class="px-5 py-3 text-right">Hạn TT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($invoices as $inv)
                    <tr>
                        <td class="px-5 py-3 text-xs text-gray-500">
                            {{ \Carbon\Carbon::parse($inv->transaction_date)->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 text-sm text-gray-800">{{ $inv->description ?? 'Học phí' }}</td>
                        <td class="px-5 py-3 text-right text-sm font-semibold text-gray-800">
                            {{ number_format($inv->amount) }}đ
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($inv->status === 'paid')
                                <span class="text-xs px-2 py-0.5 bg-green-50 text-green-700">✓ Đã TT</span>
                            @elseif($inv->status === 'pending' && $inv->due_date && \Carbon\Carbon::parse($inv->due_date)->isPast())
                                <span class="text-xs px-2 py-0.5 bg-red-50 text-red-600">⚠ Quá hạn</span>
                            @else
                                <span class="text-xs px-2 py-0.5 bg-yellow-50 text-yellow-700">⏳ Chưa TT</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right text-xs text-gray-400">
                            {{ $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d/m/Y') : '—' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">Chưa có hóa đơn.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
