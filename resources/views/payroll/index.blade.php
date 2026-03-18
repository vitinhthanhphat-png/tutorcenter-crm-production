<x-app-layout>
    <x-slot name="heading">Bảng Lương</x-slot>
    <x-slot name="subheading">Tính và quản lý lương giáo viên / trợ giảng theo tháng</x-slot>

    <div class="p-6 space-y-5">

        @if(session('success'))
        <div class="px-4 py-2 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif
        @if(session('error'))
        <div class="px-4 py-2 bg-red-50 border border-red-200 text-red-600 text-sm">{{ session('error') }}</div>
        @endif

        {{-- Month filter --}}
        <form method="GET" class="flex gap-2 items-center">
            <label class="text-sm text-gray-500">Tháng:</label>
            <input type="month" name="month" value="{{ $month }}"
                   class="border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:border-indigo-400">
            <button type="submit" class="bg-gray-100 border border-gray-200 px-4 py-1.5 text-sm">Xem</button>
        </form>

        <div class="flex flex-col lg:flex-row gap-5">

            {{-- Generate payroll panel --}}
            <div class="lg:w-72 bg-white border border-gray-100 p-5 self-start space-y-4">
                <h3 class="text-sm font-semibold text-gray-700">Tạo phiếu lương</h3>
                <p class="text-xs text-gray-400">Hệ thống tự tính số buổi dạy từ dữ liệu điểm danh.</p>
                <form method="POST" action="{{ route('payroll.generate') }}" class="space-y-3">
                    @csrf
                    <input type="hidden" name="month" value="{{ $month }}">
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Giáo viên</label>
                        <select name="user_id" required
                                class="w-full border border-gray-200 px-2 py-1.5 text-sm focus:outline-none focus:border-indigo-400">
                            <option value="">— Chọn —</option>
                            @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Lương cứng</label>
                            <input type="number" name="base_salary" min="0" value="0"
                                   class="w-full border border-gray-200 px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Lương/buổi</label>
                            <input type="number" name="rate_per_session" min="0" value="0"
                                   class="w-full border border-gray-200 px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Thưởng</label>
                            <input type="number" name="bonus" min="0" value="0"
                                   class="w-full border border-gray-200 px-2 py-1.5 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-400 mb-1">Khấu trừ</label>
                            <input type="number" name="deduction" min="0" value="0"
                                   class="w-full border border-gray-200 px-2 py-1.5 text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs text-gray-400 mb-1">Ghi chú</label>
                        <input type="text" name="note"
                               class="w-full border border-gray-200 px-2 py-1.5 text-sm">
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white py-2 text-sm hover:bg-indigo-700 transition-colors">
                        Tính lương
                    </button>
                </form>
            </div>

            {{-- Payroll table --}}
            <div class="flex-1">
                <div class="bg-white border border-gray-100 overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100 text-xs text-gray-400 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left">Giáo viên</th>
                                <th class="px-4 py-3 text-center">Số buổi</th>
                                <th class="px-4 py-3 text-right">Lương buổi</th>
                                <th class="px-4 py-3 text-right">Lương cứng</th>
                                <th class="px-4 py-3 text-right">Thưởng</th>
                                <th class="px-4 py-3 text-right font-semibold">Tổng cộng</th>
                                <th class="px-4 py-3 text-center">Trạng thái</th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($payrolls as $p)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-900">{{ $p->teacher?->name }}</td>
                                <td class="px-4 py-3 text-center text-gray-500">{{ $p->total_sessions }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($p->session_pay) }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($p->base_salary) }}</td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($p->bonus) }}</td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">{{ number_format($p->total) }}đ</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="text-xs px-2 py-0.5 {{ $p->statusColor() }}">{{ $p->statusLabel() }}</span>
                                </td>
                                <td class="px-4 py-3 flex gap-1 items-center justify-end">
                                    @if($p->status === 'draft')
                                    <form method="POST" action="{{ route('payroll.confirm', $p) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-blue-500 hover:text-blue-700">Xác nhận</button>
                                    </form>
                                    @endif
                                    @if($p->status === 'confirmed')
                                    <form method="POST" action="{{ route('payroll.markPaid', $p) }}">
                                        @csrf
                                        <button type="submit" class="text-xs text-green-600 hover:text-green-700">Đã TT</button>
                                    </form>
                                    @endif
                                    @if($p->status === 'draft')
                                    <form method="POST" action="{{ route('payroll.destroy', $p) }}"
                                          onsubmit="return confirm('Xóa phiếu nháp này?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-red-400 hover:text-red-600">Xóa</button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-4 py-8 text-center text-gray-300">
                                    Chưa có phiếu lương nào cho tháng {{ $month }}
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $payrolls->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
