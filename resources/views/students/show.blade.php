<x-app-layout>
    <x-slot name="heading">{{ $student->name }}</x-slot>
    <x-slot name="subheading">
        {!! $student->status === 'lead' ? '<span class="text-amber-600">Lead</span>' : '<span class="text-green-600">'.ucfirst($student->status).'</span>' !!}
        · {{ $student->phone ?? 'Chưa có SĐT' }}
    </x-slot>
    <x-slot name="actions">
        <a href="{{ route('pdf.student', $student) }}" target="_blank"
           class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            Xuất PDF
        </a>
        <a href="{{ route('grades.student', $student) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 border border-gray-200 text-sm text-gray-600 hover:bg-gray-50 transition-colors">
            📊 Bảng điểm
        </a>
        <a href="{{ route('students.edit', $student) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 bg-red-600 text-white text-sm font-medium hover:bg-red-700 transition-colors">
            Sửa thông tin
        </a>
        <a href="{{ route('students.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách</a>
    </x-slot>

    <div class="p-8">
        <div class="grid grid-cols-3 gap-6">

            {{-- ===== Left: Student Info ===== --}}
            <div class="col-span-1 space-y-4">
                {{-- Profile Card --}}
                <div class="bg-white border border-gray-100 p-5">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-gray-900 flex items-center justify-center text-white text-lg font-semibold flex-shrink-0">
                            {{ mb_substr($student->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">{{ $student->name }}</p>
                            @if($student->dob)
                            <p class="text-xs text-gray-400">{{ $student->dob->format('d/m/Y') }} ({{ $student->dob->age }} tuổi)</p>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2.5 text-sm">
                        @foreach([
                            ['📱 SĐT', $student->phone],
                            ['🏫 Trường', $student->school],
                            ['📍 Chi nhánh', $student->branch?->name],
                            ['📣 Nguồn', ucfirst(str_replace('_', ' ', $student->lead_source ?? '—'))],
                            ['🎯 Tư vấn', ucfirst(str_replace('_', ' ', $student->lead_status ?? '—'))],
                        ] as [$label, $value])
                        <div class="flex justify-between">
                            <span class="text-gray-400">{{ $label }}</span>
                            <span class="text-gray-700 font-medium">{{ $value ?? '—' }}</span>
                        </div>
                        @endforeach
                    </div>

                    @if($student->notes)
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <p class="text-xs text-gray-400 mb-1">Ghi chú</p>
                        <p class="text-sm text-gray-600">{{ $student->notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ===== Right: Enrollments + History ===== --}}
            <div class="col-span-2 space-y-5">

                {{-- Enrollments --}}
                <div class="bg-white border border-gray-100">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">
                            Lịch sử đăng ký học
                        </h2>
                        <span class="text-xs text-gray-400">{{ $student->enrollments->count() }} lớp</span>
                    </div>
                    @if($student->enrollments->isEmpty())
                    <div class="px-5 py-8 text-center text-gray-400 text-sm">Chưa đăng ký lớp học nào.</div>
                    @else
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-50 bg-gray-50">
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Lớp học</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Học phí</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Đã đóng</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Còn nợ</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Trạng thái</th>
                                <th class="px-5 py-2.5 text-left text-xs font-medium text-gray-400">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($student->enrollments as $enrollment)
                            @php $debt = $enrollment->final_price - $enrollment->paid_amount; @endphp
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-sm font-medium text-gray-900">
                                    <a href="{{ route('grades.class', $enrollment->classroom) }}" class="hover:text-red-600 transition-colors">{{ $enrollment->classroom?->name ?? '—' }}</a>
                                    @if($enrollment->status === 'reserved')
                                    <div class="text-xs text-blue-500 mt-0.5">
                                        Bảo lưu đến {{ $enrollment->reservation_ends_at?->format('d/m/Y') }}
                                    </div>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-sm text-gray-600">{{ number_format($enrollment->final_price) }}đ</td>
                                <td class="px-5 py-3 text-sm text-green-600 font-medium">{{ number_format($enrollment->paid_amount) }}đ</td>
                                <td class="px-5 py-3 text-sm {{ $debt > 0 ? 'text-red-600 font-semibold' : 'text-gray-400' }}">
                                    {{ $debt > 0 ? number_format($debt).'đ' : '✓ Đã thanh toán' }}
                                </td>
                                <td class="px-5 py-3">
                                    @php
                                        $eMap = [
                                            'active'    => ['Đang học',   'bg-green-50 text-green-700'],
                                            'completed' => ['Hoàn thành', 'bg-gray-100 text-gray-500'],
                                            'dropped'   => ['Nghỉ',       'bg-red-50 text-red-700'],
                                            'reserved'  => ['Bảo lưu',    'bg-blue-50 text-blue-700'],
                                            'cancelled' => ['Hủy',        'bg-gray-100 text-gray-400'],
                                        ];
                                        $e = $eMap[$enrollment->status] ?? ['?','bg-gray-100'];
                                    @endphp
                                    <span class="text-xs px-2 py-0.5 font-medium {{ $e[1] }}">{{ $e[0] }}</span>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex items-center gap-2">
                                        @if($enrollment->status === 'active')
                                        <a href="{{ route('reservations.create', $enrollment) }}"
                                           class="text-xs text-amber-600 hover:text-amber-800 hover:underline">Bảo lưu</a>
                                        @elseif($enrollment->status === 'reserved')
                                        <form method="POST" action="{{ route('reservations.reactivate', $enrollment) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="text-xs text-green-600 hover:text-green-800 hover:underline">Kích hoạt lại</button>
                                        </form>
                                        @endif
                                        <a href="{{ route('grades.class', $enrollment->classroom) }}" class="text-xs text-gray-400 hover:text-gray-600">Bảng điểm</a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </div>

                {{-- Payment History --}}
                @php $allInvoices = $student->enrollments->flatMap->invoices->sortByDesc('transaction_date'); @endphp
                @if($allInvoices->isNotEmpty())
                <div class="bg-white border border-gray-100">
                    <div class="px-5 py-4 border-b border-gray-100">
                        <h2 class="text-sm font-semibold text-gray-900" style="font-family:'Space Grotesk',sans-serif">Lịch sử thanh toán</h2>
                    </div>
                    <table class="w-full">
                        <tbody class="divide-y divide-gray-50">
                            @foreach($allInvoices as $inv)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-5 py-3 text-xs font-mono text-gray-400">{{ $inv->invoice_code }}</td>
                                <td class="px-5 py-3 text-sm text-gray-600">{{ $inv->transaction_date->format('d/m/Y') }}</td>
                                <td class="px-5 py-3 text-sm font-semibold text-gray-900">{{ number_format($inv->amount) }}đ</td>
                                <td class="px-5 py-3 text-xs text-gray-400">{{ $inv->cashier?->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
