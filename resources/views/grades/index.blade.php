<x-app-layout>
    <x-slot name="heading">Bảng Điểm</x-slot>
    <x-slot name="subheading">Chọn lớp để xem bảng điểm chi tiết</x-slot>

    <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($classes as $cls)
            <a href="{{ route('grades.class', $cls->id) }}"
               class="bg-white border border-gray-100 hover:border-red-200 hover:shadow-sm transition-all p-5 block group">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 bg-red-50 flex items-center justify-center text-red-600 font-semibold text-sm flex-shrink-0">
                        {{ mb_substr($cls->name, 0, 2) }}
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900 truncate group-hover:text-red-600">{{ $cls->name }}</h3>
                        <p class="text-xs text-gray-400 truncate">{{ $cls->course->name ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex items-center justify-between text-xs text-gray-400">
                    <span>GV: {{ $cls->teacher->name ?? '—' }}</span>
                    <span>{{ $cls->branch->name ?? '' }}</span>
                </div>
                <div class="flex items-center justify-between mt-2 text-xs">
                    <span class="px-2 py-0.5 {{ $cls->status === 'active' ? 'bg-green-50 text-green-700' : 'bg-gray-50 text-gray-500' }}">
                        {{ $cls->status === 'active' ? '● Đang học' : ucfirst($cls->status) }}
                    </span>
                    <span class="text-gray-400">Xem điểm →</span>
                </div>
            </a>
            @empty
            <div class="col-span-full">
                <p class="text-center text-gray-400 py-12">Chưa có lớp học nào.</p>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
