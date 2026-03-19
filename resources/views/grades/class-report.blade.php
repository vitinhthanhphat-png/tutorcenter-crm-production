<x-app-layout>
    <x-slot name="heading">Bảng điểm lớp học</x-slot>
    <x-slot name="subheading">{{ $classroom->name }} — Ma trận điểm danh & điểm số</x-slot>
    <x-slot name="actions">
        <a href="{{ route('pdf.attendance', $classroom) }}"
           target="_blank"
           class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Xuất PDF
        </a>
        <a href="{{ route('classes.show', $classroom) }}"
           class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            ← Về lớp học
        </a>
    </x-slot>

    <div class="p-8 space-y-6">

        {{-- Info Header --}}
        <div class="bg-white border border-gray-100 p-5 grid grid-cols-3 gap-5">
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Giáo viên</p>
                <p class="text-sm font-semibold text-gray-900 mt-1">{{ $classroom->teacher?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Tổng buổi học</p>
                <p class="text-2xl font-bold text-gray-900 mt-1" style="font-family:'Space Grotesk',sans-serif">{{ $sessions->count() }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-wide">Học sinh tham gia</p>
                <p class="text-2xl font-bold text-red-600 mt-1" style="font-family:'Space Grotesk',sans-serif">{{ $enrollments->count() }}</p>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex items-center gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1"><span class="w-5 h-5 bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs">✓</span>Có mặt</span>
            <span class="flex items-center gap-1"><span class="w-5 h-5 bg-red-100 text-red-700 flex items-center justify-center font-bold text-xs">✗</span>Vắng KP</span>
            <span class="flex items-center gap-1"><span class="w-5 h-5 bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs">L</span>Muộn</span>
            <span class="flex items-center gap-1"><span class="w-5 h-5 bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs">P</span>Có phép</span>
        </div>

        {{-- Grade Matrix Table --}}
        <div class="bg-white border border-gray-100 overflow-x-auto">
            <table class="w-full text-xs border-collapse">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 sticky left-0 bg-gray-50 border-r border-gray-100 min-w-[160px]">Học sinh</th>
                        @foreach($sessions as $session)
                        <th class="px-2 py-3 text-center text-xs font-medium text-gray-400 min-w-[48px] border-r border-gray-50" title="{{ $session->date }}">
                            {{ \Carbon\Carbon::parse($session->date)->format('d/m') }}
                        </th>
                        @endforeach
                        <th class="px-3 py-3 text-center text-xs font-medium text-green-600 bg-green-50 border-r border-gray-100">CM</th>
                        <th class="px-3 py-3 text-center text-xs font-medium text-red-600 bg-red-50 border-r border-gray-100">Vắng</th>
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 bg-gray-50">Tỷ lệ</th>
                        <th class="px-3 py-3 text-center text-xs font-medium text-gray-500">Điểm TB</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($enrollments as $enrollment)
                    @php
                        $student = $enrollment->student;
                        $studentAtts = $attendances->get($student->id, collect());
                        $presentCount = 0; $absentCount = 0; $totalGrade = 0; $gradeCount = 0;
                        $statusStyle = [
                            'present'           => ['✓', 'bg-green-50 text-green-700'],
                            'absent_no_leave'   => ['✗', 'bg-red-50 text-red-700'],
                            'absent_with_leave' => ['P',  'bg-blue-50 text-blue-700'],
                            'late'              => ['L',  'bg-amber-50 text-amber-700'],
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2.5 sticky left-0 bg-white border-r border-gray-100">
                            <a href="{{ route('grades.student', $student) }}" class="text-sm font-medium text-gray-900 hover:text-red-600 transition-colors">
                                {{ $student->name }}
                            </a>
                        </td>
                        @foreach($sessions as $session)
                        @php
                            $att = $studentAtts->firstWhere('session_id', $session->id);
                            if ($att) {
                                [$sym, $cls] = $statusStyle[$att->status] ?? ['?', ''];
                                if (in_array($att->status, ['present', 'late'])) $presentCount++;
                                if ($att->status === 'absent_no_leave') $absentCount++;
                                if ($att->grade !== null) { $totalGrade += $att->grade; $gradeCount++; }
                            } else {
                                $sym = '—'; $cls = 'text-gray-300';
                            }
                        @endphp
                        <td class="px-1 py-2.5 text-center border-r border-gray-50">
                            <span class="inline-flex items-center justify-center w-6 h-6 text-xs font-bold {{ $cls ?? '' }}" title="{{ $att?->teacher_comment ?? '' }}">{{ $sym }}</span>
                        </td>
                        @endforeach
                        @php $total = $sessions->count(); @endphp
                        <td class="px-3 py-2.5 text-center font-semibold text-green-700 bg-green-50 border-r border-gray-100">{{ $presentCount }}</td>
                        <td class="px-3 py-2.5 text-center font-semibold text-red-600 bg-red-50 border-r border-gray-100">{{ $absentCount }}</td>
                        <td class="px-3 py-2.5 text-center text-gray-700 bg-gray-50">
                            {{ $total > 0 ? round($presentCount / $total * 100) : 0 }}%
                        </td>
                        <td class="px-3 py-2.5 text-center font-semibold text-gray-900">
                            {{ $gradeCount > 0 ? number_format($totalGrade / $gradeCount, 1) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
