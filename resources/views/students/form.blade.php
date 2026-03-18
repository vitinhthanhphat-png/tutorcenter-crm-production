<x-app-layout>
    <x-slot name="heading">{{ isset($student) ? 'Sửa thông tin' : 'Thêm học sinh / Lead' }}</x-slot>
    <x-slot name="subheading">{{ isset($student) ? $student->name : 'Điền thông tin học sinh hoặc khách hàng tiềm năng' }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('students.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Quay lại danh sách</a>
    </x-slot>

    <div class="p-8 max-w-2xl">
        <form method="POST"
              action="{{ isset($student) ? route('students.update', $student) : route('students.store') }}"
              class="bg-white border border-gray-100 p-6 space-y-5">
            @csrf
            @if(isset($student)) @method('PUT') @endif

            @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm space-y-0.5">
                @foreach($errors->all() as $error)
                <p>• {{ $error }}</p>
                @endforeach
            </div>
            @endif

            {{-- Row 1: Name + Phone --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Họ và tên <span class="text-red-600">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $student->name ?? '') }}"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400"
                           placeholder="Nguyễn Văn A">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Số điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $student->phone ?? '') }}"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400"
                           placeholder="09xx xxx xxx">
                </div>
            </div>

            {{-- Row 2: DOB + School --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Ngày sinh</label>
                    <input type="date" name="dob" value="{{ old('dob', isset($student?->dob) ? $student->dob->format('Y-m-d') : '') }}"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Trường học</label>
                    <input type="text" name="school" value="{{ old('school', $student->school ?? '') }}"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400"
                           placeholder="THPT Lê Quý Đôn">
                </div>
            </div>

            {{-- Status + Lead Source --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái</label>
                    <select name="status" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        @foreach(['lead'=>'Lead / Tiềm năng','studying'=>'Đang học','reserved'=>'Bảo lưu','dropped'=>'Nghỉ học','graduated'=>'Tốt nghiệp'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('status', $student->status ?? 'lead') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Nguồn khách hàng</label>
                    <select name="lead_source" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        @foreach(['' => '— Chưa rõ —', 'facebook'=>'Facebook', 'tiktok'=>'TikTok','google'=>'Google','referral'=>'Người giới thiệu','walk_in'=>'Walk-in','zalo'=>'Zalo','other'=>'Khác'] as $v=>$l)
                        <option value="{{ $v }}" {{ old('lead_source', $student->lead_source ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Lead Status --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái tư vấn</label>
                <select name="lead_status" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                    @foreach(['' => '— Chưa rõ —', 'new'=>'Mới', 'contacted'=>'Đã liên hệ','consulted'=>'Đã tư vấn','demo'=>'Học thử','converted'=>'Đã đăng ký','lost'=>'Mất khách'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('lead_status', $student->lead_status ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Notes --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Ghi chú</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400 resize-none"
                          placeholder="Nhu cầu, lịch rảnh, mục tiêu học...">{{ old('notes', $student->notes ?? '') }}</textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
                    {{ isset($student) ? 'Lưu thay đổi' : 'Thêm học sinh' }}
                </button>
                <a href="{{ route('students.index') }}" class="px-5 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">Hủy</a>

                @if(isset($student))
                <div class="flex-1 flex justify-end">
                    <form method="POST" action="{{ route('students.destroy', $student) }}" class="inline" onsubmit="return confirm('Xóa học sinh này?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-500 hover:text-red-700">Xóa học sinh</button>
                    </form>
                </div>
                @endif
            </div>
        </form>
    </div>
</x-app-layout>
