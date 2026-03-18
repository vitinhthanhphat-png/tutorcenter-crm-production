<x-app-layout>
    <x-slot name="heading">{{ $class->name }}</x-slot>
    <x-slot name="subheading">{{ $class->course?->name }} · {{ $class->branch?->name }}</x-slot>
    <x-slot name="actions">
        <a href="{{ route('classes.edit', $class) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            Sửa thông tin
        </a>
        <button onclick="document.getElementById('enrollModal').classList.remove('hidden')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
            + Ghi danh học sinh
        </button>
        <a href="{{ route('classes.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách lớp</a>
    </x-slot>

    <div class="p-8 space-y-6">

        @if(session('success'))
        <div class="px-4 py-3 bg-green-50 border border-green-200 text-green-700 text-sm">{{ session('success') }}</div>
        @endif

        <div class="grid grid-cols-3 gap-6">

            {{-- ===== LEFT: Class Info ===== --}}
            <div class="col-span-1 space-y-4">
                <div class="bg-white border border-gray-100 p-5 space-y-3">
                    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Thông tin lớp</h3>
                    @php
                    $details = [
                        ['Khóa học',   $class->course?->name],
                        ['Giáo viên',  $class->teacher?->name],
                        ['Trợ giảng',  $class->tutor?->name],
                        ['Phòng',      $class->room_name],
                        ['Chi nhánh',  $class->branch?->name],
                        ['Sĩ số',      count($enrollments).' / '.($class->max_students ?? '∞').' học sinh'],
                    ];
                    if($class->schedule_rule) {
                        $days = implode(', ', array_map(fn($d) => ucfirst(substr($d,0,2)), $class->schedule_rule['days'] ?? []));
                        $details[] = ['Lịch', $days.' · '.$class->schedule_rule['start_time'].'-'.$class->schedule_rule['end_time']];
                    }
                    @endphp
                    @foreach($details as [$label, $value])
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-400">{{ $label }}</span>
                        <span class="text-gray-700 font-medium text-right">{{ $value ?? '—' }}</span>
                    </div>
                    @endforeach

                    {{-- Status --}}
                    <div class="flex justify-between items-center text-sm pt-2 border-t border-gray-50">
                        <span class="text-gray-400">Trạng thái</span>
                        @php $sMap = ['active'=>['Đang mở','bg-green-50 text-green-700'],'planned'=>['Chuẩn bị','bg-amber-50 text-amber-700'],'completed'=>['Kết thúc','bg-gray-100 text-gray-500'],'cancelled'=>['Hủy','bg-red-50 text-red-700']]; $s=$sMap[$class->status]??['?','bg-gray-100']; @endphp
                        <span class="text-xs px-2 py-0.5 font-medium {{ $s[1] }}">{{ $s[0] }}</span>
                    </div>
                </div>
            </div>

            {{-- ===== RIGHT: Students + Sessions ===== --}}
            <div class="col-span-2 space-y-5">

                {{-- Enrolled Students --}}
                <div class="bg-white border border-gray-100">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">
                            Danh sách học sinh
                        </h2>
                        <span class="text-xs text-gray-400">{{ $enrollments->count() }} học sinh</span>
                    </div>
                    @if($enrollments->isEmpty())
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">Chưa có học sinh nào. Nhấn "Ghi danh học sinh" để thêm.</div>
                    @else
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50 bg-gray-50">
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Học sinh</th>
                                <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-400">Học phí</th>
                                <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-400">Đã đóng</th>
                                <th class="px-5 py-2.5 text-right text-xs font-medium text-gray-400">Còn nợ</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">TT</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($enrollments as $enr)
                            @php $debt = $enr->final_price - $enr->paid_amount; @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3">
                                    <a href="{{ route('students.show', $enr->student) }}"
                                       class="text-sm font-medium text-gray-900 hover:text-red-600">
                                        {{ $enr->student?->name }}
                                    </a>
                                    <p class="text-xs text-gray-400">{{ $enr->student?->phone }}</p>
                                </td>
                                <td class="px-5 py-3 text-sm text-right text-gray-600">{{ number_format($enr->final_price) }}đ</td>
                                <td class="px-5 py-3 text-sm text-right text-green-600 font-medium">{{ number_format($enr->paid_amount) }}đ</td>
                                <td class="px-5 py-3 text-sm text-right {{ $debt > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                    {{ $debt > 0 ? number_format($debt).'đ' : '✓' }}
                                </td>
                                <td class="px-5 py-3">
                                    @php $eMap=['active'=>['Đang học','bg-green-50 text-green-700'],'completed'=>['Xong','bg-gray-100 text-gray-500'],'dropped'=>['Nghỉ','bg-red-50 text-red-700']]; $e=$eMap[$enr->status]??['?','bg-gray-100']; @endphp
                                    <span class="text-xs font-medium px-2 py-0.5 {{ $e[1] }}">{{ $e[0] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                {{-- Recent Sessions --}}
                <div class="bg-white border border-gray-100">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Buổi học gần nhất</h2>
                    </div>
                    @if($class->sessions->isEmpty())
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">Chưa có buổi học nào.</div>
                    @else
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50 bg-gray-50">
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Buổi</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Ngày</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Giờ</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Trợ giảng</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Trạng thái</th>
                                <th class="px-5 py-2.5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($class->sessions as $i => $session)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-sm text-gray-400">#{{ $class->sessions->count() - $i }}</td>
                                <td class="px-5 py-3 text-sm text-gray-900">{{ $session->date->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-sm text-gray-500">{{ $session->start_time }} – {{ $session->end_time }}</td>
                                <td class="px-5 py-3 text-sm text-gray-500">{{ $session->tutor?->name ?? '—' }}</td>
                                <td class="px-5 py-3">
                                    @php $sm=['scheduled'=>['Chưa điểm danh','bg-amber-50 text-amber-700'],'completed'=>['Đã điểm danh','bg-green-50 text-green-700'],'cancelled'=>['Hủy','bg-gray-100 text-gray-400']]; $ss=$sm[$session->status]??[$session->status,'bg-gray-100']; @endphp
                                    <span class="text-xs font-medium px-2 py-0.5 {{ $ss[1] }}">{{ $ss[0] }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    @if(in_array($session->status, ['scheduled', 'pending']))
                                    <a href="{{ route('attendance.show', $session) }}"
                                       class="text-xs text-red-600 hover:underline font-medium">Điểm danh →</a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ===== ENROLL MODAL ===== --}}
    <div id="enrollModal" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-md p-6 shadow-xl">
            <div class="flex items-center justify-between mb-5">
                <h3 class="text-base font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Ghi danh vào: {{ $class->name }}</h3>
                <button onclick="document.getElementById('enrollModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            <form method="POST" action="{{ route('enrollments.store') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="class_id" value="{{ $class->id }}">

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Học sinh <span class="text-red-600">*</span></label>
                    <select name="student_id" required class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                        <option value="">Chọn học sinh...</option>
                        @foreach(\App\Models\Student::orderBy('name')->get() as $s)
                        <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->phone ?? 'chưa SĐT' }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Học phí</label>
                        <input type="number" name="final_price"
                               value="{{ $class->course?->price ?? 0 }}"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Đã đóng (đặt cọc)</label>
                        <input type="number" name="paid_amount" value="0"
                               class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Ngày bắt đầu</label>
                    <input type="date" name="start_date" value="{{ now()->format('Y-m-d') }}"
                           class="w-full border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:border-gray-400">
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
                        Xác nhận ghi danh
                    </button>
                    <button type="button" onclick="document.getElementById('enrollModal').classList.add('hidden')"
                            class="flex-1 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
                        Hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
