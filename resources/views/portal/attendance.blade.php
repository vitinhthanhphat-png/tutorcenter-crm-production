<x-app-layout>
    <x-slot name="heading">Lịch sử Điểm danh</x-slot>
    <x-slot name="subheading">Xem toàn bộ điểm danh của {{ $student->name }}</x-slot>

    <div class="p-6 space-y-4">

        {{-- Summary badges --}}
        <div class="flex gap-3 flex-wrap">
            <span class="px-3 py-1.5 text-xs bg-green-50 text-green-700 border border-green-100">
                ✓ Có mặt: <strong>{{ $summary['present'] ?? 0 }}</strong>
            </span>
            <span class="px-3 py-1.5 text-xs bg-red-50 text-red-600 border border-red-100">
                ✗ Vắng: <strong>{{ $summary['absent'] ?? 0 }}</strong>
            </span>
            <span class="px-3 py-1.5 text-xs bg-yellow-50 text-yellow-700 border border-yellow-100">
                ⏰ Muộn: <strong>{{ $summary['late'] ?? 0 }}</strong>
            </span>
            <span class="px-3 py-1.5 text-xs bg-blue-50 text-blue-600 border border-blue-100">
                ~ Vắng phép: <strong>{{ $summary['excused'] ?? 0 }}</strong>
            </span>
        </div>

        {{-- Month filter --}}
        <form method="GET" class="flex gap-2 items-center">
            <input type="month" name="month" value="{{ request('month') }}"
                   class="border border-gray-200 px-3 py-1.5 text-sm focus:outline-none focus:border-red-400">
            <button class="bg-gray-800 text-white text-xs px-4 py-2 hover:bg-gray-700">Lọc</button>
            @if(request('month'))
            <a href="{{ route('portal.attendance') }}" class="text-xs text-gray-400 hover:text-red-500">Xóa bộ lọc</a>
            @endif
        </form>

        {{-- Attendance table --}}
        <div class="bg-white border border-gray-100">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-400 font-medium">
                        <th class="px-5 py-3 text-left">Ngày</th>
                        <th class="px-5 py-3 text-left">Lớp học</th>
                        <th class="px-5 py-3 text-left">Khóa học</th>
                        <th class="px-5 py-3 text-center">Trạng thái</th>
                        <th class="px-5 py-3 text-left">Ghi chú GV</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($records as $a)
                    <tr>
                        <td class="px-5 py-3 text-xs text-gray-600">
                            {{ \Carbon\Carbon::parse($a->session->date ?? now())->format('d/m/Y') }}
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-800 font-medium">{{ $a->session->classroom->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $a->session->classroom->course->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">
                            @php
                                $badge = match($a->status) {
                                    'present' => ['bg-green-50 text-green-700', '✓ Có mặt'],
                                    'absent'  => ['bg-red-50 text-red-600', '✗ Vắng'],
                                    'late'    => ['bg-yellow-50 text-yellow-700', '⏰ Muộn'],
                                    'excused' => ['bg-blue-50 text-blue-600', '~ Phép'],
                                    default   => ['bg-gray-50 text-gray-600', $a->status],
                                };
                            @endphp
                            <span class="text-xs px-2 py-0.5 {{ $badge[0] }}">{{ $badge[1] }}</span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-400">{{ $a->note ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-sm text-gray-400">Không có dữ liệu điểm danh.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-5 py-3 border-t border-gray-100">
                {{ $records->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
