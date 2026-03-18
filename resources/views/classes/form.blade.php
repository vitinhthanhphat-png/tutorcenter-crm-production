<x-app-layout>
    <x-slot name="heading">{{ isset($class) ? 'Chỉnh sửa lớp học' : 'Tạo lớp học mới' }}</x-slot>
    <x-slot name="subheading">{{ isset($class) ? $class->name : 'Điền thông tin lớp học bên dưới' }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('classes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Quay lại</a>
    </x-slot>

    <div class="p-8 max-w-2xl">
        <form method="POST"
              action="{{ isset($class) ? route('classes.update', $class) : route('classes.store') }}"
              class="bg-white border border-gray-100 p-6 space-y-5">
            @csrf
            @if(isset($class)) @method('PUT') @endif

            @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 text-red-700 text-sm">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            {{-- Name --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tên lớp <span class="text-red-600">*</span></label>
                <input type="text" name="name" value="{{ old('name', $class->name ?? '') }}"
                    class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400"
                    placeholder="VD: IELTS 6.0 - K12A">
            </div>

            {{-- Course --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Khóa học <span class="text-red-600">*</span></label>
                <select name="course_id" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                    <option value="">Chọn khóa học...</option>
                    @foreach($courses as $course)
                    <option value="{{ $course->id }}" {{ old('course_id', $class->course_id ?? '') == $course->id ? 'selected' : '' }}>
                        {{ $course->name }} — {{ number_format($course->price) }}đ
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- Branch + Teacher --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Chi nhánh <span class="text-red-600">*</span></label>
                    <select name="branch_id" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ old('branch_id', $class->branch_id ?? '') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Giáo viên</label>
                    <select name="teacher_id" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        <option value="">Chưa phân công</option>
                        @foreach($teachers as $teacher)
                        <option value="{{ $teacher->id }}" {{ old('teacher_id', $class->teacher_id ?? '') == $teacher->id ? 'selected' : '' }}>{{ $teacher->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Room + Max --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Phòng học</label>
                    <input type="text" name="room_name" value="{{ old('room_name', $class->room_name ?? '') }}"
                        class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400"
                        placeholder="P.101">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Sĩ số tối đa</label>
                    <input type="number" name="max_students" value="{{ old('max_students', $class->max_students ?? 20) }}"
                        class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400"
                        min="1" max="100">
                </div>
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Trạng thái</label>
                <select name="status" class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                    @foreach(['planned'=>'Chuẩn bị','active'=>'Đang mở','completed'=>'Kết thúc','cancelled'=>'Hủy'] as $val=>$label)
                    <option value="{{ $val }}" {{ old('status', $class->status ?? 'active') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
                    {{ isset($class) ? 'Lưu thay đổi' : 'Tạo lớp học' }}
                </button>
                <a href="{{ route('classes.index') }}" class="px-5 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">Hủy</a>
            </div>
        </form>
    </div>
</x-app-layout>
