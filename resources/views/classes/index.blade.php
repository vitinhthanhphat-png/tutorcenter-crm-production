<x-app-layout>
    <x-slot name="heading">Quản lý Lớp học</x-slot>
    <x-slot name="subheading">Danh sách lớp học của trung tâm</x-slot>
    <x-slot name="actions">
        <a href="{{ route('classes.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Thêm lớp học
        </a>
    </x-slot>

    <div class="p-8">

        {{-- Alerts --}}
        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
        @endif

        {{-- Filter bar --}}
        <div class="flex items-center gap-3 mb-5">
            <input type="text" placeholder="Tìm kiếm lớp học..."
                   class="border border-gray-200 px-3 py-2 text-sm w-60 focus:outline-none focus:border-gray-400">
            <select class="border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                <option value="">Tất cả trạng thái</option>
                <option value="active">Đang mở</option>
                <option value="planned">Chuẩn bị</option>
                <option value="completed">Đã kết thúc</option>
            </select>
        </div>

        {{-- Table --}}
        <div class="bg-white border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Tên lớp</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Khóa học</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Giáo viên</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Học sinh</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Lịch học</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($classes as $class)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3.5">
                            <p class="text-sm font-medium text-gray-900">{{ $class->name }}</p>
                            <p class="text-xs text-gray-400">{{ $class->branch?->name }}</p>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">{{ $class->course?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-sm text-gray-600">{{ $class->teacher?->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-sm text-gray-900 font-medium">{{ $class->enrollments_count ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-xs text-gray-500">
                            @if($class->schedule_rule)
                                {{ implode(', ', array_map(fn($d) => substr(ucfirst($d),0,2), $class->schedule_rule['days'] ?? [])) }}
                                {{ $class->schedule_rule['start_time'] ?? '' }}
                            @else —
                            @endif
                        </td>
                        <td class="px-5 py-3.5">
                            @php
                            $statusMap = ['active'=>['Đang mở','bg-green-50 text-green-700'],'planned'=>['Chuẩn bị','bg-amber-50 text-amber-700'],'completed'=>['Kết thúc','bg-gray-100 text-gray-500'],'cancelled'=>['Hủy','bg-red-50 text-red-700']];
                            $s = $statusMap[$class->status] ?? ['?','bg-gray-100 text-gray-500'];
                            @endphp
                            <span class="px-2 py-0.5 text-xs font-medium {{ $s[1] }}">{{ $s[0] }}</span>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('classes.edit', $class) }}" class="text-gray-400 hover:text-gray-700" title="Sửa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <a href="{{ route('classes.show', $class) }}" class="text-gray-400 hover:text-blue-600" title="Xem">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm">Chưa có lớp học nào. <a href="{{ route('classes.create') }}" class="text-red-600 hover:underline">Tạo lớp đầu tiên</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $classes->links() }}
        </div>

    </div>
</x-app-layout>
