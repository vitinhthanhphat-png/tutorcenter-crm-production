<x-app-layout>
    <x-slot name="heading">Bảng điểm học sinh</x-slot>
    <x-slot name="subheading">{{ $student->name }} — Lịch sử điểm & điểm danh cá nhân</x-slot>
    <x-slot name="actions">
        <a href="{{ route('pdf.student', $student) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Xuất PDF hồ sơ
        </a>
        <a href="{{ route('students.show', $student) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            ← Về học sinh
        </a>
    </x-slot>

    <div class="p-8 space-y-6">

        {{-- Student info --}}
        <div class="bg-white border border-gray-100 p-5 flex items-center gap-5">
            <div class="w-12 h-12 bg-red-50 flex items-center justify-center text-lg font-bold text-red-600">
                {{ mb_substr($student->name, 0, 1) }}
            </div>
            <div>
                <p class="text-base font-semibold text-gray-900">{{ $student->name }}</p>
                <p class="text-sm text-gray-400">{{ $student->phone ?? '—' }} · {{ $student->branch?->name ?? '—' }}</p>
            </div>
        </div>

        {{-- Per-enrollment sections --}}
        @foreach($student->enrollments as $enrollment)
        @php
            $classroom = $enrollment->classroom;
            if (!$classroom) continue;
            $sessions = $classroom->sessions->sortBy('date');
        @endphp

        <div class="bg-white border border-gray-100 overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-semibold text-gray-900">{{ $classroom->name }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Ghi danh {{ $enrollment->created_at->format('d/m/Y') }}</p>
                </div>
                <a href="{{ route('grades.class', $classroom) }}" class="text-xs text-red-600 hover:underline">Xem bảng điểm lớp →</a>
            </div>
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Buổi</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Ngày</th>
                        <th class="px-5 py-2.5 text-center text-xs font-medium text-gray-400 uppercase tracking-wide">Điểm danh</th>
                        <th class="px-5 py-2.5 text-center text-xs font-medium text-gray-400 uppercase tracking-wide">Điểm số</th>
                        <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Nhận xét GV</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sessions as $i => $session)
                    @php
                        $att = $attendances->get($session->id);
                        $statusMap = [
                            'present'           => ['Có mặt',   'bg-green-50 text-green-700'],
                            'absent_no_leave'   => ['Vắng KP',  'bg-red-50 text-red-700'],
                            'absent_with_leave' => ['Có phép',  'bg-blue-50 text-blue-700'],
                            'late'              => ['Muộn',     'bg-amber-50 text-amber-700'],
                        ];
                        [$label, $cls] = $att ? ($statusMap[$att->status] ?? ['?', '']) : ['—', 'text-gray-300'];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-500">Buổi {{ $i + 1 }}</td>
                        <td class="px-5 py-3 text-sm text-gray-700">{{ \Carbon\Carbon::parse($session->date)->format('d/m/Y') }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($att)
                            <span class="px-2 py-0.5 text-xs font-medium {{ $cls }}">{{ $label }}</span>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($att && $att->grade !== null)
                            <span class="text-sm font-bold text-gray-900">{{ $att->grade }}</span>
                            @else
                            <span class="text-gray-300 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500 italic">{{ $att?->teacher_comment ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-6 text-center text-gray-400 text-sm">Chưa có buổi học nào.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @endforeach

    </div>
</x-app-layout>
