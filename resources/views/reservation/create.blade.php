<x-app-layout>
    <x-slot name="heading">Bảo lưu khóa học</x-slot>
    <x-slot name="subheading">{{ $enrollment->student->name }} — {{ $enrollment->classroom?->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('students.show', $enrollment->student_id) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            ← Hủy, quay lại
        </a>
    </x-slot>

    <div class="p-8">
        <div class="max-w-lg mx-auto">

            {{-- Warning banner --}}
            <div class="bg-amber-50 border border-amber-200 px-4 py-3 mb-6 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
                <div>
                    <p class="text-sm font-medium text-amber-800">Bảo lưu khóa học</p>
                    <p class="text-xs text-amber-600 mt-0.5">Học sinh sẽ tạm ngừng học trong khoảng thời gian bảo lưu. Học phí đã đóng sẽ được bảo toàn và tính khi quay lại.</p>
                </div>
            </div>

            {{-- Current enrollment info --}}
            <div class="bg-white border border-gray-100 p-5 mb-6 space-y-3">
                <h3 class="text-xs font-medium text-gray-400 uppercase tracking-wide">Thông tin ghi danh hiện tại</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400">Học phí khóa học</p>
                        <p class="font-semibold text-gray-900">{{ number_format($enrollment->final_price) }}đ</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Đã thanh toán</p>
                        <p class="font-semibold text-green-600">{{ number_format($enrollment->paid_amount) }}đ</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Còn nợ</p>
                        <p class="font-semibold text-red-600">{{ number_format(max(0, $enrollment->final_price - $enrollment->paid_amount)) }}đ</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Trạng thái</p>
                        <span class="px-2 py-0.5 text-xs bg-green-50 text-green-700 font-medium">{{ $enrollment->status }}</span>
                    </div>
                </div>
            </div>

            {{-- Reservation Form --}}
            <form method="POST" action="{{ route('reservations.store', $enrollment) }}" class="bg-white border border-gray-100 p-6 space-y-5">
                @csrf

                @if($errors->any())
                <div class="px-4 py-3 bg-red-50 border border-red-200 text-red-700 text-sm">
                    @foreach($errors->all() as $error)
                    <p>{{ $error }}</p>
                    @endforeach
                </div>
                @endif

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Ngày bắt đầu bảo lưu <span class="text-red-500">*</span></label>
                    <input type="date" name="reserved_at" value="{{ old('reserved_at', now()->format('Y-m-d')) }}" required
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Ngày kết thúc bảo lưu <span class="text-red-500">*</span></label>
                    <input type="date" name="reservation_ends_at" value="{{ old('reservation_ends_at', now()->addMonths(3)->format('Y-m-d')) }}" required
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                    <p class="text-xs text-gray-400 mt-1">Mặc định 3 tháng. Sau ngày này, học sinh có thể được kích hoạt lại.</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1.5">Ghi chú bảo lưu</label>
                    <textarea name="reservation_note" rows="3"
                              placeholder="Lý do bảo lưu, ghi chú học phí, thỏa thuận đặc biệt..."
                              class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400 resize-none">{{ old('reservation_note') }}</textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit"
                            class="px-5 py-2 bg-amber-500 text-white text-sm font-medium hover:bg-amber-600 transition-colors">
                        Xác nhận bảo lưu
                    </button>
                    <a href="{{ route('students.show', $enrollment->student_id) }}"
                       class="px-5 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                        Hủy
                    </a>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
