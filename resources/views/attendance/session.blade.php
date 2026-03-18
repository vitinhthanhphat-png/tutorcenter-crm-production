<x-app-layout>
    <x-slot name="heading">Điểm danh: {{ $session->classroom?->name }}</x-slot>
    <x-slot name="subheading">Buổi {{ $session->date->format('d/m/Y') }} — {{ $session->start_time }} đến {{ $session->end_time }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('classes.show', $session->class_id) }}" class="text-sm text-gray-500 hover:text-gray-700">← Quay lại lớp</a>
    </x-slot>

    <div class="p-8">

        @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        {{-- Session Info --}}
        <div class="grid grid-cols-4 gap-4 mb-6">
            @php
            $total   = $attendances->count();
            $present = $attendances->where('status', 'present')->count();
            $leave   = $attendances->where('status', 'absent_with_leave')->count();
            $noLeave = $attendances->where('status', 'absent_no_leave')->count();
            @endphp
            @foreach([['Tổng số', $total, 'bg-gray-100 text-gray-700'], ['Có mặt', $present, 'bg-green-50 text-green-700'], ['Nghỉ có phép', $leave, 'bg-amber-50 text-amber-700'], ['Nghỉ không phép', $noLeave, 'bg-red-50 text-red-700']] as [$label, $count, $style])
            <div class="bg-white border border-gray-100 px-5 py-4">
                <p class="text-xs text-gray-400">{{ $label }}</p>
                <p class="text-2xl font-bold mt-1 {{ $style }} px-2 py-0.5 inline-block" style="font-family:'Space Grotesk',sans-serif">{{ $count }}</p>
            </div>
            @endforeach
        </div>

        {{-- Attendance Form --}}
        <form method="POST" action="{{ route('attendance.store', $session) }}" class="bg-white border border-gray-100">
            @csrf
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide w-10">#</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Tên học sinh</th>
                        <th class="px-5 py-3 text-center text-xs font-medium text-gray-400 uppercase tracking-wide">Trạng thái</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide w-24">Điểm BT</th>
                        <th class="px-5 py-3 text-left text-xs font-medium text-gray-400 uppercase tracking-wide">Nhận xét</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($attendances as $studentId => $att)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-5 py-3 text-sm text-gray-400">{{ $loop->iteration }}</td>
                        <td class="px-5 py-3">
                            <p class="text-sm font-medium text-gray-900">{{ $att->student?->name }}</p>
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-center gap-1">
                                @foreach([
                                    'present'          => ['✓', 'Có mặt',      'bg-green-100 text-green-700 border-green-200'],
                                    'absent_with_leave'=> ['P', 'Nghỉ phép',   'bg-amber-100 text-amber-700 border-amber-200'],
                                    'absent_no_leave'  => ['X', 'Vắng',       'bg-red-100 text-red-700 border-red-200'],
                                    'late'             => ['L', 'Đi trễ',     'bg-blue-100 text-blue-700 border-blue-200'],
                                ] as $value => [$symbol, $title, $style])
                                <label title="{{ $title }}" class="cursor-pointer">
                                    <input type="radio" name="attendance[{{ $studentId }}][status]"
                                           value="{{ $value }}"
                                           {{ ($att->status ?? 'present') === $value ? 'checked' : '' }}
                                           class="sr-only peer">
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold border rounded-sm cursor-pointer transition-all
                                                 peer-checked:{{ $style }} peer-checked:ring-1 peer-checked:ring-current
                                                 border-gray-200 text-gray-400 hover:border-gray-300">
                                        {{ $symbol }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <input type="number" name="attendance[{{ $studentId }}][grade]"
                                   value="{{ $att->grade }}" min="0" max="10" step="0.5"
                                   class="border border-gray-200 px-2 py-1 w-16 text-sm text-center focus:outline-none focus:border-gray-400">
                        </td>
                        <td class="px-5 py-3">
                            <input type="text" name="attendance[{{ $studentId }}][comment]"
                                   value="{{ $att->teacher_comment }}"
                                   placeholder="Nhận xét..."
                                   class="border border-gray-200 px-2 py-1 w-full text-sm focus:outline-none focus:border-gray-400">
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="flex items-center justify-between px-5 py-4 border-t border-gray-100">
                <p class="text-xs text-gray-400">Cập nhật lần cuối: {{ $session->updated_at->format('d/m/Y H:i') }}</p>
                <button type="submit" class="px-6 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
                    Lưu điểm danh
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
