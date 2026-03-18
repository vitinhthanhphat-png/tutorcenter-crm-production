<x-app-layout>
    <x-slot name="heading">Chuyển Lớp</x-slot>
    <x-slot name="subheading">{{ $enrollment->student?->name }} đang học lớp {{ $enrollment->classroom?->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('students.show', $enrollment->student_id) }}"
           class="text-sm text-gray-500 hover:text-gray-700">← Hồ sơ học sinh</a>
    </x-slot>

    <div class="p-8 max-w-lg">

        @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 text-sm">
            <ul>@foreach($errors->all() as $e)<li>• {{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        {{-- Current enrollment summary --}}
        <div class="bg-yellow-50 border border-yellow-200 p-4 mb-5 text-sm">
            <p class="font-medium text-yellow-800 mb-2">📋 Thông tin ghi danh hiện tại</p>
            <div class="grid grid-cols-2 gap-1 text-xs text-yellow-700">
                <span>Lớp hiện tại:</span>
                <span class="font-medium">{{ $enrollment->classroom?->name }}</span>
                <span>Học phí:</span>
                <span>{{ number_format($enrollment->final_price) }}đ</span>
                <span>Đã đóng:</span>
                <span>{{ number_format($enrollment->paid_amount) }}đ</span>
                <span>Còn dư:</span>
                <span class="font-medium {{ $enrollment->paid_amount > $enrollment->final_price ? 'text-green-700' : '' }}">
                    {{ number_format(max(0, $enrollment->paid_amount - $enrollment->final_price)) }}đ
                </span>
            </div>
            @if($enrollment->paid_amount > $enrollment->final_price)
            <p class="mt-2 text-xs text-green-700 font-medium">
                ✓ Số dư dư sẽ được chuyển sang lớp mới như credit.
            </p>
            @endif
        </div>

        <div class="bg-white border border-gray-100 p-6">
            <form method="POST" action="{{ route('enrollments.transfer.store', $enrollment) }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">
                            Lớp muốn chuyển sang <span class="text-red-500">*</span>
                        </label>
                        <select name="target_class_id" required
                                class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400">
                            <option value="">— Chọn lớp mới —</option>
                            @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ old('target_class_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }}
                                @if($c->schedule_rule)
                                  · {{ implode('/', array_map(fn($d) => strtoupper(substr($d,0,2)), $c->schedule_rule['days'] ?? [])) }}
                                @endif
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Lý do chuyển lớp</label>
                        <textarea name="transfer_note" rows="3" placeholder="Lý do chuyển, ghi chú thêm..."
                                  class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-red-400 resize-none">{{ old('transfer_note') }}</textarea>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 p-3 text-xs text-gray-500 space-y-1">
                        <p>⚠️ <strong>Lưu ý khi chuyển lớp:</strong></p>
                        <p>• Ghi danh hiện tại sẽ được đánh dấu là <em>Đã chuyển lớp</em>.</p>
                        <p>• Số dư tiền học (nếu có) sẽ được ghi nhận là credit cho lớp mới.</p>
                        <p>• Học sinh sẽ bắt đầu học và điểm danh ở lớp mới từ hôm nay.</p>
                    </div>

                    <div class="flex gap-3 pt-1">
                        <button type="submit"
                                class="bg-red-600 text-white px-6 py-2 text-sm hover:bg-red-700 transition-colors"
                                onclick="return confirm('Xác nhận chuyển lớp? Hành động này không thể hoàn tác.')">
                            Xác nhận chuyển lớp
                        </button>
                        <a href="{{ route('students.show', $enrollment->student_id) }}"
                           class="px-5 py-2 text-sm border border-gray-200 hover:bg-gray-50">Hủy</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
